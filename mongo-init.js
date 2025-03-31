db = db.getSiblingDB('crm');

// Create collections
db.createCollection('users');
db.createCollection('tickets');
db.createCollection('clients');
db.createCollection('comments');
db.createCollection('activities');

// Create indexes
db.users.createIndex({ email: 1 }, { unique: true });
db.tickets.createIndex({ clientId: 1 });
db.comments.createIndex({ ticketId: 1 });
db.activities.createIndex({ ticketId: 1 });

// Insert sample data
db.users.insertOne({
  name: "Admin User",
  email: "admin@example.com",
  password: "$2a$10$oCXC1/C.NPHw9X1A1YU9qeUKVwi2D8f6xhAL4DqP1SbKxW3Jrny7m", // "password" hashed
  role: "admin",
  createdAt: new Date()
});

db.clients.insertMany([
  {
    name: "Acme Corp",
    email: "contact@acmecorp.com",
    phone: "123-456-7890",
    createdAt: new Date()
  },
  {
    name: "XYZ Industries",
    email: "support@xyz.com",
    phone: "987-654-3210",
    createdAt: new Date()
  }
]);

// Insert sample tickets
const acmeId = db.clients.findOne({ name: "Acme Corp" })._id;
const xyzId = db.clients.findOne({ name: "XYZ Industries" })._id;
const adminId = db.users.findOne({ email: "admin@example.com" })._id;

db.tickets.insertMany([
  {
    subject: "Website not loading",
    description: "The company website is not loading properly in Chrome.",
    status: "Open",
    priority: "High",
    clientId: acmeId,
    createdBy: adminId,
    createdAt: new Date()
  },
  {
    subject: "Email service down",
    description: "Email service is not working since this morning.",
    status: "Pending",
    priority: "Urgent",
    clientId: xyzId,
    createdBy: adminId,
    createdAt: new Date()
  }
]);