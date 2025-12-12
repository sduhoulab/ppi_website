-- Create table
CREATE TABLE protome (
    Accession VARCHAR(20),
    Protein_Name VARCHAR(255),
    Gene VARCHAR(50),
    Sequence_Length INT,
    Interaction_Residues_Model VARCHAR(255),
    Interaction_Residues_PDB VARCHAR(255),
    Interaction_Residues_Alphafold VARCHAR(255)
);

-- Insert rows
INSERT INTO protome (
    Accession, Protein_Name, Gene, Sequence_Length,
    Interaction_Residues_Model, Interaction_Residues_PDB, Interaction_Residues_Alphafold
)
VALUES
('Q9BTV7', 'CDK5 and ABL1 enzyme substrate 2', 'CABLES2', 478,
 '26S,27H,29K', '26S,27H,29K', '26S,27H,29K'),

('Q9C0C6', 'CLOCK-interacting pacemaker', 'CIPC', 399,
 '26S,27H,29K', '26S,27H,29K', '-'),

('Q6PG37', 'Zinc finger protein 790', 'ZNF790', 636,
 '26S,27H,29K', '-', '26S,27H,29K'),

('Q16548', 'Bcl-2-related protein A1', 'BCL2A1', 175,
 '26S,27H,29K', '26S,27H,29K', '26S,27H,29K'),

('Q9Y4W6', 'Mitochondrial inner membrane m-AAA protease component AFG3L2', 'AFG3L2', 797,
 '26S,27H,29K', '26S,27H,29K', '26S,27H,29K');