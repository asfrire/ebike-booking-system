import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, Alert, StyleSheet, ScrollView, RefreshControl } from 'react-native';
import { riderAPI, bookingAPI } from '../services/api';

export default function RiderScreen({ navigation }: any) {
  const [user, setUser] = useState<any>(null);
  const [isOnline, setIsOnline] = useState(false);
  const [capacity, setCapacity] = useState(2);
  const [assignments, setAssignments] = useState<any[]>([]);
  const [queuePosition, setQueuePosition] = useState<number | null>(null);
  const [stats, setStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      // Get user info
      const userData = await riderAPI.getRiders();
      setUser(userData[0]); // Assuming current user is first in list
      
      // Get assignments
      const assignmentsData = await riderAPI.getMyAssignments();
      setAssignments(assignmentsData);
      
      // Get queue position
      const positionData = await riderAPI.getQueuePosition();
      setQueuePosition(positionData.position);
      
      // Get stats
      const statsData = await riderAPI.getStats();
      setStats(statsData);
      
      // Set online status and capacity from user data
      setIsOnline(userData[0]?.is_online || false);
      setCapacity(userData[0]?.capacity || 2);
    } catch (error) {
      Alert.alert('Error', 'Failed to load rider data');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleGoOnline = async () => {
    try {
      await riderAPI.goOnline();
      setIsOnline(true);
      Alert.alert('Success', 'You are now online and available for bookings');
      loadData();
    } catch (error) {
      Alert.alert('Error', 'Failed to go online');
    }
  };

  const handleGoOffline = async () => {
    Alert.alert(
      'Go Offline',
      'Are you sure you want to go offline? You will not receive new bookings.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Go Offline',
          style: 'destructive',
          onPress: async () => {
            try {
              await riderAPI.goOffline();
              setIsOnline(false);
              Alert.alert('Success', 'You are now offline');
              loadData();
            } catch (error) {
              Alert.alert('Error', 'Failed to go offline');
            }
          },
        },
      ]
    );
  };

  const handleUpdateCapacity = async () => {
    Alert.alert(
      'Update Capacity',
      `Current capacity: ${capacity} passengers`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Set to 3',
          onPress: async () => {
            try {
              await riderAPI.updateCapacity(3);
              setCapacity(3);
              Alert.alert('Success', 'Capacity updated to 3 passengers');
              loadData();
            } catch (error) {
              Alert.alert('Error', 'Failed to update capacity');
            }
          },
        },
        {
          text: 'Set to 4',
          onPress: async () => {
            try {
              await riderAPI.updateCapacity(4);
              setCapacity(4);
              Alert.alert('Success', 'Capacity updated to 4 passengers');
              loadData();
            } catch (error) {
              Alert.alert('Error', 'Failed to update capacity');
            }
          },
        },
        {
          text: 'Set to 5',
          onPress: async () => {
            try {
              await riderAPI.updateCapacity(5);
              setCapacity(5);
              Alert.alert('Success', 'Capacity updated to 5 passengers');
              loadData();
            } catch (error) {
              Alert.alert('Error', 'Failed to update capacity');
            }
          },
        },
      ]
    );
  };

  const handleAcceptAssignment = async (bookingId: number) => {
    try {
      await bookingAPI.acceptBooking(bookingId);
      Alert.alert('Success', 'Booking accepted successfully');
      loadData();
    } catch (error) {
      Alert.alert('Error', 'Failed to accept booking');
    }
  };

  const handleRejectAssignment = async (bookingId: number) => {
    Alert.alert(
      'Reject Booking',
      'Are you sure you want to reject this booking?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Reject',
          style: 'destructive',
          onPress: async () => {
            try {
              await bookingAPI.rejectBooking(bookingId);
              Alert.alert('Success', 'Booking rejected');
              loadData();
            } catch (error) {
              Alert.alert('Error', 'Failed to reject booking');
            }
          },
        },
      ]
    );
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
        <Text style={styles.welcomeText}>Rider Dashboard</Text>
        <Text style={styles.statusText}>
          Status: <Text style={[isOnline ? styles.onlineText : styles.offlineText]}>
            {isOnline ? 'Online' : 'Offline'}
          </Text>
        </Text>
      </View>

      <View style={styles.actions}>
        {!isOnline ? (
          <TouchableOpacity style={[styles.button, styles.onlineButton]} onPress={handleGoOnline}>
            <Text style={styles.buttonText}>Go Online</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity style={[styles.button, styles.offlineButton]} onPress={handleGoOffline}>
            <Text style={styles.buttonText}>Go Offline</Text>
          </TouchableOpacity>
        )}
        
        <TouchableOpacity style={[styles.button, styles.capacityButton]} onPress={handleUpdateCapacity}>
          <Text style={styles.buttonText}>Capacity: {capacity}</Text>
        </TouchableOpacity>
      </View>

      <View style={styles.stats}>
        <Text style={styles.sectionTitle}>Statistics</Text>
        <View style={styles.statRow}>
          <Text style={styles.statLabel}>Queue Position:</Text>
          <Text style={styles.statValue}>
            {queuePosition !== null ? `#${queuePosition}` : 'Not in queue'}
          </Text>
        </View>
        <View style={styles.statRow}>
          <Text style={styles.statLabel}>Active Assignments:</Text>
          <Text style={styles.statValue}>{assignments.length}</Text>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Current Assignments</Text>
        {assignments.length === 0 ? (
          <Text style={styles.emptyText}>No current assignments</Text>
        ) : (
          assignments.map((assignment: any) => (
            <View key={assignment.id} style={styles.assignmentCard}>
              <View style={styles.assignmentHeader}>
                <Text style={styles.assignmentId}>Booking #{assignment.booking_id}</Text>
                <Text style={[styles.assignmentStatus, { 
                  color: assignment.status === 'pending' ? '#f39c12' : '#27ae60' 
                }]}>
                  {assignment.status}
                </Text>
              </View>
              <Text style={styles.assignmentLocation}>
                From: {assignment.booking?.pickup_location}
              </Text>
              <Text style={styles.assignmentLocation}>
                To: {assignment.booking?.dropoff_location}
              </Text>
              <Text style={styles.assignmentDetails}>
                Passengers: {assignment.allocated_seats}
              </Text>
              
              {assignment.status === 'pending' && (
                <View style={styles.assignmentActions}>
                  <TouchableOpacity 
                    style={[styles.actionButton, styles.acceptButton]} 
                    onPress={() => handleAcceptAssignment(assignment.booking_id)}
                  >
                    <Text style={styles.actionButtonText}>Accept</Text>
                  </TouchableOpacity>
                  <TouchableOpacity 
                    style={[styles.actionButton, styles.rejectButton]} 
                    onPress={() => handleRejectAssignment(assignment.booking_id)}
                  >
                    <Text style={styles.actionButtonText}>Reject</Text>
                  </TouchableOpacity>
                </View>
              )}
            </View>
          ))
        )}
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
  },
  welcomeText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  statusText: {
    fontSize: 16,
    color: '#7f8c8d',
    marginTop: 5,
  },
  onlineText: {
    color: '#27ae60',
    fontWeight: 'bold',
  },
  offlineText: {
    color: '#e74c3c',
    fontWeight: 'bold',
  },
  actions: {
    padding: 20,
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  button: {
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    flex: 1,
    marginHorizontal: 5,
  },
  onlineButton: {
    backgroundColor: '#27ae60',
  },
  offlineButton: {
    backgroundColor: '#e74c3c',
  },
  capacityButton: {
    backgroundColor: '#3498db',
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  stats: {
    padding: 20,
    backgroundColor: '#fff',
    margin: 20,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#eee',
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 15,
  },
  statRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  statLabel: {
    fontSize: 16,
    color: '#7f8c8d',
  },
  statValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  section: {
    padding: 20,
  },
  emptyText: {
    textAlign: 'center',
    color: '#7f8c8d',
    fontSize: 16,
  },
  assignmentCard: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 8,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#eee',
  },
  assignmentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  assignmentId: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  assignmentStatus: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  assignmentLocation: {
    fontSize: 16,
    color: '#2c3e50',
    marginBottom: 5,
  },
  assignmentDetails: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 10,
  },
  assignmentActions: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  actionButton: {
    padding: 10,
    borderRadius: 6,
    alignItems: 'center',
    flex: 1,
    marginHorizontal: 5,
  },
  acceptButton: {
    backgroundColor: '#27ae60',
  },
  rejectButton: {
    backgroundColor: '#e74c3c',
  },
  actionButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
});
