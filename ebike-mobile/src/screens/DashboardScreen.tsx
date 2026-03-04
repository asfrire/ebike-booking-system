import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, Alert, StyleSheet, ScrollView, RefreshControl } from 'react-native';
import { authAPI, bookingAPI } from '../services/api';

export default function DashboardScreen({ navigation }: any) {
  const [user, setUser] = useState<any>(null);
  const [bookings, setBookings] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const userData = await authAPI.getMe();
      setUser(userData.user);
      
      const bookingsData = await bookingAPI.getBookings();
      setBookings(bookingsData);
    } catch (error) {
      Alert.alert('Error', 'Failed to load data');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleLogout = async () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Logout',
          style: 'destructive',
          onPress: async () => {
            try {
              await authAPI.logout();
              navigation.replace('Login');
            } catch (error) {
              Alert.alert('Error', 'Failed to logout');
            }
          },
        },
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending': return '#f39c12';
      case 'assigned': return '#3498db';
      case 'accepted': return '#27ae60';
      case 'completed': return '#2c3e50';
      case 'cancelled': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  if (loading) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      <View style={styles.header}>
        <Text style={styles.welcomeText}>Welcome, {user?.name}!</Text>
        <Text style={styles.roleText}>Role: {user?.role}</Text>
      </View>

      <View style={styles.actions}>
        <TouchableOpacity 
          style={styles.actionButton} 
          onPress={() => navigation.navigate('Booking')}
        >
          <Text style={styles.actionButtonText}>Create Booking</Text>
        </TouchableOpacity>
        
        {user?.role === 'admin' && (
          <TouchableOpacity 
            style={[styles.actionButton, styles.adminButton]} 
            onPress={() => navigation.navigate('Rider')}
          >
            <Text style={styles.actionButtonText}>Rider Dashboard</Text>
          </TouchableOpacity>
        )}
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Your Bookings</Text>
        {bookings.length === 0 ? (
          <Text style={styles.emptyText}>No bookings yet</Text>
        ) : (
          bookings.map((booking) => (
            <View key={booking.id} style={styles.bookingCard}>
              <View style={styles.bookingHeader}>
                <Text style={styles.bookingId}>Booking #{booking.id}</Text>
                <Text style={[styles.bookingStatus, { color: getStatusColor(booking.status) }]}>
                  {booking.status}
                </Text>
              </View>
              <Text style={styles.bookingLocation}>From: {booking.pickup_location}</Text>
              <Text style={styles.bookingLocation}>To: {booking.dropoff_location}</Text>
              <Text style={styles.bookingDetails}>Passengers: {booking.pax}</Text>
              <Text style={styles.bookingDetails}>Remaining: {booking.remaining_pax}</Text>
            </View>
          ))
        )}
      </View>

      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
        <Text style={styles.logoutButtonText}>Logout</Text>
      </TouchableOpacity>
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
  },
  welcomeText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  roleText: {
    fontSize: 16,
    color: '#7f8c8d',
    marginTop: 5,
  },
  actions: {
    padding: 20,
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  actionButton: {
    backgroundColor: '#3498db',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    flex: 1,
    marginHorizontal: 5,
  },
  adminButton: {
    backgroundColor: '#9b59b6',
  },
  actionButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  section: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 15,
  },
  emptyText: {
    textAlign: 'center',
    color: '#7f8c8d',
    fontSize: 16,
  },
  bookingCard: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 8,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#eee',
  },
  bookingHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  bookingId: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  bookingStatus: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  bookingLocation: {
    fontSize: 16,
    color: '#2c3e50',
    marginBottom: 5,
  },
  bookingDetails: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  logoutButton: {
    backgroundColor: '#e74c3c',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    margin: 20,
  },
  logoutButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
