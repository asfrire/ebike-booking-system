import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, ScrollView } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { bookingAPI } from '../services/api';

export default function BookingScreen({ navigation }: any) {
  const [pickupLocation, setPickupLocation] = useState('');
  const [dropoffLocation, setDropoffLocation] = useState('');
  const [pax, setPax] = useState('2');
  const [loading, setLoading] = useState(false);

  const handleCreateBooking = async () => {
    if (!pickupLocation || !dropoffLocation) {
      Alert.alert('Error', 'Please fill in pickup and dropoff locations');
      return;
    }

    const passengers = parseInt(pax);
    if (passengers < 1 || passengers > 5) {
      Alert.alert('Error', 'Number of passengers must be between 1 and 5');
      return;
    }

    setLoading(true);
    try {
      const bookingData = {
        pickup_location: pickupLocation,
        dropoff_location: dropoffLocation,
        pax: passengers,
      };

      const response = await bookingAPI.createBooking(bookingData);
      Alert.alert('Success', 'Booking created successfully!');
      navigation.goBack();
    } catch (error: any) {
      Alert.alert('Booking Failed', 
        error.response?.data?.message || 'Failed to create booking'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Create Booking</Text>
        <Text style={styles.subtitle}>Book your e-bike ride</Text>
      </View>

      <View style={styles.form}>
        <Text style={styles.label}>Pickup Location</Text>
        <TextInput
          style={styles.input}
          placeholder="Enter pickup location"
          value={pickupLocation}
          onChangeText={setPickupLocation}
        />

        <Text style={styles.label}>Dropoff Location</Text>
        <TextInput
          style={styles.input}
          placeholder="Enter dropoff location"
          value={dropoffLocation}
          onChangeText={setDropoffLocation}
        />

        <Text style={styles.label}>Number of Passengers</Text>
        <View style={styles.pickerContainer}>
          <Picker
            selectedValue={pax}
            onValueChange={setPax}
            style={styles.picker}
          >
            <Picker.Item label="1 Passenger" value="1" />
            <Picker.Item label="2 Passengers" value="2" />
            <Picker.Item label="3 Passengers" value="3" />
            <Picker.Item label="4 Passengers" value="4" />
            <Picker.Item label="5 Passengers" value="5" />
          </Picker>
        </View>

        <View style={styles.infoBox}>
          <Text style={styles.infoTitle}>Booking Information</Text>
          <Text style={styles.infoText}>• Each e-bike can carry 2-5 passengers</Text>
          <Text style={styles.infoText}>• Riders will be assigned automatically</Text>
          <Text style={styles.infoText}>• You'll receive notifications on updates</Text>
          <Text style={styles.infoText}>• Payment is handled after completion</Text>
        </View>

        <TouchableOpacity 
          style={[styles.button, loading && styles.buttonDisabled]} 
          onPress={handleCreateBooking}
          disabled={loading}
        >
          <Text style={styles.buttonText}>
            {loading ? 'Creating Booking...' : 'Create Booking'}
          </Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    padding: 20,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
    alignItems: 'center',
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  subtitle: {
    fontSize: 16,
    color: '#7f8c8d',
    marginTop: 5,
  },
  form: {
    padding: 20,
  },
  label: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 8,
  },
  input: {
    height: 50,
    borderColor: '#ddd',
    borderWidth: 1,
    borderRadius: 8,
    marginBottom: 20,
    paddingHorizontal: 15,
    backgroundColor: '#fff',
  },
  pickerContainer: {
    height: 50,
    borderColor: '#ddd',
    borderWidth: 1,
    borderRadius: 8,
    marginBottom: 20,
    backgroundColor: '#fff',
    justifyContent: 'center',
  },
  picker: {
    height: 50,
  },
  infoBox: {
    backgroundColor: '#ecf0f1',
    padding: 15,
    borderRadius: 8,
    marginBottom: 20,
  },
  infoTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 10,
  },
  infoText: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 5,
  },
  button: {
    backgroundColor: '#27ae60',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
  },
  buttonDisabled: {
    backgroundColor: '#bdc3c7',
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
