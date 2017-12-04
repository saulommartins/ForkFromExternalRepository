/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
 * SiamProposta.java
 *
 * Programa para gerar Formulario de preenchimento de proposta (compra Direta)
 * lendo e editando um XML criado na inserção de compra direta
 *
 * Criado em 8 de Agosto de 2007, 09:57
 * Modificado em 23 de Janeiro de 2009 , 16:45 
 *
 *	Analista: Gelson W
 *	Desenvolvedor: Luiz Felipe P Teixeira
 *
   	$Id: $
 * 
 */

package urbem;

import java.awt.Component;
import javax.swing.filechooser.*;
import javax.swing.DefaultCellEditor;
import javax.swing.JFormattedTextField;
import javax.swing.JOptionPane;
import javax.swing.table.*;

import java.io.File;
import java.util.*;
import javax.swing.JTextField;
import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;
import javax.swing.text.MaskFormatter;

import org.hamcrest.core.IsInstanceOf;

import com.sun.org.apache.xml.internal.dtm.ref.EmptyIterator;

/**
 *SiamProposta
 * 
 * Classe principal do programa onde sera inicializado os componentes do form
 * e sera gerado o form e toda a estrutura do sistema de preenchimento de cotações
 * para compra direta
 * 
 *  @extends javax.swing.JFRAME
 */
@SuppressWarnings("serial")
public class SiamProposta extends javax.swing.JFrame 
{        
	/**
	 * Declaração de Propriedades da classe
	 */
    private javax.swing.JButton jButton1;
    private javax.swing.JButton jButton2;
    private javax.swing.JButton jButton3;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel10;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JLabel jLabel4;
    private javax.swing.JLabel jLabel5;
    private javax.swing.JLabel jLabel6;
    private javax.swing.JLabel jLabel7;
    private javax.swing.JLabel jLabel8;
    private javax.swing.JLabel jLabel9;
    private javax.swing.JMenu jMenu1;
    private javax.swing.JMenuBar jMenuBar1;
    private javax.swing.JMenuItem jMenuItem1;
    private javax.swing.JMenuItem jMenuItem2;
    private javax.swing.JMenuItem jMenuItem3;
    private javax.swing.JMenuItem jMenuItem4;
    private javax.swing.JMenuItem jMenuItem5;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTextField jTextField1;
    private javax.swing.JTextField jTextField10;
    private javax.swing.JTextField jTextField2;
    private javax.swing.JTextField jTextField3;
    private javax.swing.JTextField jTextField4;
    private javax.swing.JTextField jTextField5;
    private javax.swing.JTextField jTextField6;
    private javax.swing.JTextField jTextField7;
    private javax.swing.JTextField jTextField8;
    private javax.swing.JTextField jTextField9;
    private javax.swing.JToolBar jToolBar1;
    private javax.swing.JTable tabelaItens;
    private JFormattedTextField jTextFieldDataJtable;
    private XML xml;
	
	/**
	 *  Construtor da classe SiamProposta
	 *  
	 *  Cria o Form principal
	 *  
	 */
    public SiamProposta() 
    {
        initComponents();
        
        setExtendedState(MAXIMIZED_BOTH);  
        
        for(final Component c: jPanel1.getComponents()) {
            if(c instanceof JTextField) {               
                ((JTextField) c).getDocument().addDocumentListener( new DocumentListener() {

                    public void insertUpdate(final DocumentEvent e) {
                        if(xml != null) {
                           xml.setSaved(false);
                           jButton2.setEnabled(true);
                           jMenuItem2.setEnabled(true);
                        }
                    }

                    public void removeUpdate(final DocumentEvent e) {
                        if(xml != null) {
                           xml.setSaved(false);
                           jButton2.setEnabled(true);
                           jMenuItem2.setEnabled(true);
                        }
                    }

                    public void changedUpdate(final DocumentEvent e) {
                        if(xml != null) {
                           xml.setSaved(false);
                           jButton2.setEnabled(true);
                           jMenuItem2.setEnabled(true);
                        }
                    }
                }); 
            }
        }     
    }
    
    /**
     * createFormatter
     * 
     * Metodo para criar formatos de mascara para os campos
     * 
     * @param s
     * @return string Formatada
     */
    protected MaskFormatter createFormatter(final String s) {
        MaskFormatter formatter = null;
        try {
            formatter = new MaskFormatter(s);
        } catch ( java.text.ParseException exc) {
            System.err.println("formatter is bad: " + exc.getMessage());
            System.exit(-1);
        }
        return formatter;
    }

	/**
	 * SalvarXML
	 * 
	 * Metodo para salvar o XML de cotação
	 * 
	 * @param file
	 */
    private void SalvarXML( File file) {
        getFornecedor();
        getItems();
        xml.salvar(file);
    }
    
    /** 
     * InitComponents
     * 
     * Inicializa todos os componentes que estarão no form (textfields e table, botoes de menu...)
     * @param void
     * @return void
     */
    private void initComponents() {

        jToolBar1 = new javax.swing.JToolBar();
        jButton1 = new javax.swing.JButton();
        jButton2 = new javax.swing.JButton();
        jButton3 = new javax.swing.JButton();
        jPanel1 = new javax.swing.JPanel();
        jLabel1 = new javax.swing.JLabel();
        jTextField1 = new javax.swing.JFormattedTextField( createFormatter("##.###.###/####-##") );
        jScrollPane1 = new javax.swing.JScrollPane();
        tabelaItens = new javax.swing.JTable();
        jLabel2 = new javax.swing.JLabel();
        jTextField2 = new javax.swing.JTextField(new FixedLengthPlainDocument(200), "", 200);
        jLabel3 = new javax.swing.JLabel();
        jTextField3 = new javax.swing.JTextField(new FixedLengthPlainDocument(200), "", 200);
        jLabel4 = new javax.swing.JLabel();
        jTextField4 = new javax.swing.JTextField(new FixedLengthPlainDocument(200), "", 200);
        jLabel5 = new javax.swing.JLabel();
        jTextField5 = new javax.swing.JTextField(new FixedLengthPlainDocument(60), "", 60);
        jLabel6 = new javax.swing.JLabel();
        jTextField6 = new javax.swing.JTextField(new FixedLengthPlainDocument(2), "", 2);
        jLabel7 = new javax.swing.JLabel();
        jTextField7 = new javax.swing.JFormattedTextField(createFormatter("#####-###"));
        jLabel8 = new javax.swing.JLabel();
        jTextField8 = new javax.swing.JTextField(new FixedLengthPlainDocument(60), "", 60);
        jLabel9 = new javax.swing.JLabel();
        jTextField9 = new javax.swing.JFormattedTextField(createFormatter("(##)####-####"));
        jLabel10 = new javax.swing.JLabel();
        jTextField10 = new javax.swing.JTextField(new FixedLengthPlainDocument(60), "", 60);
        
        jTextFieldDataJtable = new javax.swing.JFormattedTextField(createFormatter("##/##/####"));
        
        jMenuBar1 = new javax.swing.JMenuBar();
        jMenu1 = new javax.swing.JMenu();
        jMenuItem3 = new javax.swing.JMenuItem();
        jMenuItem2 = new javax.swing.JMenuItem();
        jMenuItem5 = new javax.swing.JMenuItem();
        jMenuItem1 = new javax.swing.JMenuItem();
        jMenuItem4 = new javax.swing.JMenuItem();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setTitle("SiamProposta");
        setName("framePrincipal");        

        /* Setando o uso dos botoes e tool bars*/
        jToolBar1.setRollover(true);

        jButton1.setText("Abrir");
        jButton1.setFocusable(false);
        jButton1.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButton1.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jButton1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jButton1ActionPerformed(evt);
            }
        });
        jToolBar1.add(jButton1);

        jButton2.setText("Salvar");
        jButton2.setEnabled(false);
        jButton2.setFocusable(false);
        jButton2.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButton2.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jButton2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jButton2ActionPerformed(evt);
            }
        });
        jToolBar1.add(jButton2);

        jButton3.setText("Fechar");
        jButton3.setEnabled(false);
        jButton3.setFocusable(false);
        jButton3.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
        jButton3.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
        jButton3.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jButton3ActionPerformed(evt);
            }
        });
        jToolBar1.add(jButton3);

        getContentPane().add(jToolBar1, java.awt.BorderLayout.PAGE_START);

        jLabel1.setText("CNPJ");

        jTextField1.setEnabled(false);

        /* setando as propriedades da tabela de itens (modelo, tamanho, mascaras)*/
        tabelaItens.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {

            },
            new String [] {
                "", "Item", "Unidade", "Marca","Data Validade Proposta", "Valor"
            }
        ) {
            @SuppressWarnings("unchecked")
			Class[] types = new Class [] {
                java.lang.Integer.class, java.lang.String.class, java.lang.String.class, java.lang.String.class, java.lang.String.class, java.lang.Float.class
            };
            boolean[] canEdit = new boolean [] {
                false, false, false, true, true,true
            };

            @SuppressWarnings("unchecked")
			public Class getColumnClass(final int columnIndex) {
                return types [columnIndex];
            }

            public boolean isCellEditable(final int rowIndex, final int columnIndex) {
                return canEdit [columnIndex];
            }
        });
                
        tabelaItens.setAutoscrolls(false);
        tabelaItens.getTableHeader().setReorderingAllowed(false);
        jScrollPane1.setViewportView(tabelaItens);
        tabelaItens.getColumnModel().getColumn(0).setPreferredWidth(15);
        tabelaItens.getColumnModel().getColumn(0).setMaxWidth(15);
        tabelaItens.getColumnModel().getColumn(1).setPreferredWidth(200);
        tabelaItens.getColumnModel().getColumn(2).setPreferredWidth(80);
        tabelaItens.getColumnModel().getColumn(2).setMaxWidth(100);
        tabelaItens.getColumnModel().getColumn(3).setPreferredWidth(120);
        tabelaItens.getColumnModel().getColumn(3).setMaxWidth(180);
        tabelaItens.getColumnModel().getColumn(4).setPreferredWidth(150);
        tabelaItens.getColumnModel().getColumn(4).setMaxWidth(150);
        tabelaItens.getColumnModel().getColumn(5).setPreferredWidth(150);
        tabelaItens.getColumnModel().getColumn(5).setMaxWidth(150);
        
        tabelaItens.getColumnModel().getColumn(4).setCellEditor(new DefaultCellEditor(jTextFieldDataJtable)); 

    	/* Setando conteudo das labels e habilitando textfields*/
        jLabel2.setText("Razão Social");

        jTextField2.setEnabled(false);

        jLabel3.setText("Nome Fantasia");

        jTextField3.setEnabled(false);

        jLabel4.setText("Endereço");

        jTextField4.setEnabled(false);

        jLabel5.setText("Cidade");

        jTextField5.setEnabled(false);

        jLabel6.setText("Estado");

        jTextField6.setEnabled(false);

        jLabel7.setText("CEP");

        jTextField7.setEnabled(false);

        jLabel8.setText("Contato");

        jTextField8.setEnabled(false);

        jLabel9.setText("Telefone");

        jTextField9.setEnabled(false);

        jLabel10.setText("E-mail");

        jTextField10.setEnabled(false);
        
    	/* Criando o painel e fixando o layout no mesmo*/
        final javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, jPanel1Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                    .addComponent(jScrollPane1, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 376, Short.MAX_VALUE)
                    .addGroup(jPanel1Layout.createSequentialGroup()
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                            .addComponent(jLabel3)
                            .addComponent(jLabel4)
                            .addComponent(jLabel2)
                            .addComponent(jLabel1)
                            .addComponent(jLabel5)
                            .addComponent(jLabel6)
                            .addComponent(jLabel7)
                            .addComponent(jLabel8)
                            .addComponent(jLabel9)
                            .addComponent(jLabel10))
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                            .addComponent(jTextField3, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 286, Short.MAX_VALUE)
                            .addComponent(jTextField4, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 286, Short.MAX_VALUE)
                            .addComponent(jTextField5, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.PREFERRED_SIZE, 186, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(jTextField2, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 286, Short.MAX_VALUE)
                            .addComponent(jTextField6, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.PREFERRED_SIZE, 48, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(jTextField8, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.DEFAULT_SIZE, 286, Short.MAX_VALUE)
                            .addComponent(jTextField10, javax.swing.GroupLayout.DEFAULT_SIZE, 286, Short.MAX_VALUE)
                            .addComponent(jTextField1, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.PREFERRED_SIZE, 147, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(jTextField7, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.PREFERRED_SIZE, 84, javax.swing.GroupLayout.PREFERRED_SIZE)
                            .addComponent(jTextField9, javax.swing.GroupLayout.Alignment.LEADING, javax.swing.GroupLayout.PREFERRED_SIZE, 103, javax.swing.GroupLayout.PREFERRED_SIZE))))
                .addContainerGap())
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel1)
                    .addComponent(jTextField1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel2)
                    .addComponent(jTextField2, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING)
                    .addComponent(jLabel3)
                    .addComponent(jTextField3, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(10, 10, 10)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel4)
                    .addComponent(jTextField4, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(10, 10, 10)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel5)
                    .addComponent(jTextField5, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel6)
                    .addComponent(jTextField6, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel7)
                    .addComponent(jTextField7, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel8)
                    .addComponent(jTextField8, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel9)
                    .addComponent(jTextField9, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel10)
                    .addComponent(jTextField10, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 222, Short.MAX_VALUE)
                .addContainerGap())
        );

        getContentPane().add(jPanel1, java.awt.BorderLayout.CENTER);

        jMenu1.setMnemonic('A');
        jMenu1.setText("Arquivo");

        jMenuItem3.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_A, java.awt.event.InputEvent.CTRL_MASK));
        jMenuItem3.setMnemonic('A');
        jMenuItem3.setText("Abrir");
        jMenuItem3.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jMenuItem3ActionPerformed(evt);
            }
        });
        jMenu1.add(jMenuItem3);

        jMenuItem2.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_S, java.awt.event.InputEvent.CTRL_MASK));
        jMenuItem2.setMnemonic('S');
        jMenuItem2.setText("Salvar");
        jMenuItem2.setEnabled(false);
        jMenuItem2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jMenuItem2ActionPerformed(evt);
            }
        });
        jMenu1.add(jMenuItem2);

        jMenuItem5.setText("Salvar Como...");
        jMenuItem5.setEnabled(false);
        jMenuItem5.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jMenuItem5ActionPerformed(evt);
            }
        });
        jMenu1.add(jMenuItem5);

        jMenuItem1.setMnemonic('F');
        jMenuItem1.setText("Fechar");
        jMenuItem1.setEnabled(false);
        jMenuItem1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jMenuItem1ActionPerformed(evt);
            }
        });
        jMenu1.add(jMenuItem1);

        jMenuItem4.setText("Sair");
        jMenuItem4.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed( java.awt.event.ActionEvent evt) {
                jMenuItem4ActionPerformed(evt);
            }
        });
        jMenu1.add(jMenuItem4);

        jMenuBar1.add(jMenu1);

        setJMenuBar(jMenuBar1);
        
        pack();
    }

	/**
	 * Evento para acionar os metodos dos botoes do menu
	 * @param evt
	 */ 
	private void jMenuItem1ActionPerformed( java.awt.event.ActionEvent evt) {
	    jButton3ActionPerformed(evt);
	}

	/**
	 * Evento para acionar os metodos dos botoes do menu
	 * @param evt
	 */
	private void jMenuItem2ActionPerformed( java.awt.event.ActionEvent evt) {
	    jButton2ActionPerformed(evt);
	}
	
	/**
	 * Evento para acionar os metodos dos botoes do menu
	 * @param evt
	 */ 
	private void jMenuItem3ActionPerformed( java.awt.event.ActionEvent evt) {
	    jButton1ActionPerformed(evt);
	}

	/**
	 * jButton2ActionPerformed
	 * 
	 * Metodo que cria um evento para salvar o arquivo xml
	 * 
	 * @param evt
	 */
	private void jButton2ActionPerformed( java.awt.event.ActionEvent evt) {
	    SalvarXML();
	    JOptionPane.showMessageDialog(this,"Salvo","Salvo",JOptionPane.INFORMATION_MESSAGE);
	}
	
	/**
	 * jButton3ActionPerformed
	 * 
	 * Metodo (evento) acionado quando se pressiona o botao fechar
	 * verifica se o arquivo XML foi salvo e depois fecha o mesmo 
	 * 
	 * @param evt
	 */
	private void jButton3ActionPerformed(final java.awt.event.ActionEvent evt) {
	    if(xml.isSaved()) {
	       FecharArquivo();
	    } else {
			   Object[] options = {"Sim", "Não", "Cancelar"};  
			   final int retval = JOptionPane.showOptionDialog(this, "Arquivo não esta salvo. Deseja salvar o arquivo?", "Deseja salvar o arquivo?", JOptionPane.YES_NO_CANCEL_OPTION, JOptionPane.QUESTION_MESSAGE, null,options, options[0]);
	       if (retval ==  JOptionPane.YES_OPTION) {
	           SalvarXML();
	           FecharArquivo();
	       }
	       else if (retval ==  JOptionPane.NO_OPTION) {
	           FecharArquivo();
	       }
	   }
	}

	/**
	 * jButton1ActionPerformed
	 * 
	 * abre a janela para a seleção do arquivo XML filtrando
	 * no sistema todos os arquivos desse tipo
	 * 
	 * @param evt
	 */
	private void jButton1ActionPerformed(java.awt.event.ActionEvent evt) {
	    javax.swing.JFileChooser chooser = new javax.swing.JFileChooser();
	    FileNameExtensionFilter filter = new FileNameExtensionFilter("Arquivos XML", "xml");
	    chooser.setAcceptAllFileFilterUsed(false);
	    chooser.setFileFilter(filter);
	    int retval = chooser.showOpenDialog(this);
	    if (retval == javax.swing.JFileChooser.APPROVE_OPTION) {
	         File file = chooser.getSelectedFile();
	        ImportarXML(file);
	    }     
	}

	/**
	 * jMenuItem5ActionPerformed
	 * 
	 * Metodo que filtra os XML no sistema operacional 
	 * na hora em que esta sendo feito a busca pelo arquivo
	 * 
	 * @param evt
	 */
	private void jMenuItem5ActionPerformed(final java.awt.event.ActionEvent evt) {
	    javax.swing.JFileChooser chooser = new javax.swing.JFileChooser();
	    FileNameExtensionFilter filter = new FileNameExtensionFilter("Arquivos XML", "xml");
	    chooser.setAcceptAllFileFilterUsed(false);
	    chooser.setFileFilter(filter);
	    int retval = chooser.showSaveDialog(this);
	    if (retval == javax.swing.JFileChooser.APPROVE_OPTION) {
	         File file = chooser.getSelectedFile();
	         SalvarXML(file);
	    }   
	}

	/**
	 * jMenuItem4ActionPerformed
	 * 
	 * Sair do programa
	 * 
	 * @param evt
	 */
	private void jMenuItem4ActionPerformed(java.awt.event.ActionEvent evt) {
		if(xml != null) {
			if(xml.isSaved()) {
				System.exit(0);
			} else {
			   Object[] options = {"Sim", "Não", "Cancelar"};  
			   final int retval = JOptionPane.showOptionDialog(this, "Arquivo não esta salvo. Deseja salvar o arquivo?", "Deseja salvar o arquivo?", JOptionPane.YES_NO_CANCEL_OPTION, JOptionPane.QUESTION_MESSAGE, null,options, options[0]);
			   if (retval ==  JOptionPane.YES_OPTION) {
			       SalvarXML();
			       System.exit(0);
			   }
			   else if (retval ==  JOptionPane.NO_OPTION) {
				   System.exit(0);
			   }
			}
		} else {
			System.exit(0);	
		}
	}

	/**
	 * getItens
	 * 
	 * Metodo para recuperar itens editais do xml
	 * para serem inseridos na table do programa
	 */
	private void getItems() 
	{
	    final List<Item> items = xml.getItems();
	    for(int i=0;i<tabelaItens.getModel().getRowCount();i++) {
	       items.get(i).marca  = (String)tabelaItens.getModel().getValueAt(i, 3);
	       items.get(i).data_validade_proposta  = (String)tabelaItens.getModel().getValueAt(i, 4);
	       items.get(i).valor  = (Float)tabelaItens.getModel().getValueAt(i, 5);
	    }
	}
	
	/**
	 * getFornecedor
	 * 
	 * Metodo para recuperar as informações do fornecedor 
	 * vindas do xml
	 */
	private void getFornecedor() 
	{
	    final Fornecedor fornecedor = xml.getFornecedor();
	    fornecedor.cnpj = jTextField1.getText();
	    fornecedor.razaoSocial = jTextField2.getText();        
	    fornecedor.nomeFantasia = jTextField3.getText();                
	    fornecedor.endereco = jTextField4.getText();                        
	    fornecedor.cidade = jTextField5.getText();                                
	    fornecedor.estado = jTextField6.getText();                                        
	    fornecedor.cep = jTextField7.getText();                                                
	    fornecedor.contato = jTextField8.getText();                                                        
	    fornecedor.telefone = jTextField9.getText();                                                                
	    fornecedor.email = jTextField10.getText();                                                                        
	}

	/**
	 * ImportarXML
	 * 
	 * Metodo para importar o xml de cotacao
	 * 
	 * @param file
	 */
	private void ImportarXML(final File file) 
	{
	  try {
		  	xml = new XML(file);        
	        jButton2.setEnabled(true);
	        jButton3.setEnabled(true);
	        jMenuItem1.setEnabled(true);
	        jMenuItem2.setEnabled(true);        
	        jMenuItem5.setEnabled(true);
	        final Fornecedor fornecedor = xml.getFornecedor();
	        final List<Item> items = xml.getItems();
	        jTextField1.setEnabled(true);
	        jTextField1.setText(fornecedor.cnpj);
	        jTextField2.setEnabled(true);
	        jTextField2.setText(fornecedor.razaoSocial);
	        jTextField3.setEnabled(true);        
	        jTextField3.setText(fornecedor.nomeFantasia);
	        jTextField4.setEnabled(true);        
	        jTextField4.setText(fornecedor.endereco);        
	        jTextField5.setEnabled(true);        
	        jTextField5.setText(fornecedor.cidade);        
	        jTextField6.setEnabled(true);        
	        jTextField6.setText(fornecedor.estado);        
	        jTextField7.setEnabled(true);        
	        jTextField7.setText(fornecedor.cep);                
	        jTextField8.setEnabled(true);        
	        jTextField8.setText(fornecedor.contato);                        
	        jTextField9.setEnabled(true);        
	        jTextField9.setText(fornecedor.telefone);                                
	        jTextField10.setEnabled(true);        
	        jTextField10.setText(fornecedor.email);                                        
	        for(int i=tabelaItens.getModel().getRowCount()-1;i>=0;i--) {
	           ((DefaultTableModel) tabelaItens.getModel()).removeRow(i);
	        }
	        int rowCount = 0;
	        for( Item item: items) {
	            ((DefaultTableModel) tabelaItens.getModel()).addRow(new Object[] { ++rowCount, item.descricaoResumida, item.unidade, item.marca,item.data_validade_proposta, item.valor});
	        }
	    } catch( Exception e) {
	            //e.printStackTrace();
	            javax.swing.JOptionPane.showMessageDialog(this, "O arquivo está errado:"+file.getName());
	    }
	}

	/**
	 * SalvarXML
	 * 
	 * Metodo que busca as informações do Fornecedor e dos Itens
	 * E apos isso acessando a classe XML salva as informações no arquivo
	 * 	
	 */
	private void SalvarXML() 
	{
	    getFornecedor();
	    getItems();
	    xml.salvar();
	}

	/**
	 * 	FecharArquivo
	 * 
	 * Metodo que limpa as variaveis quando 
	 * é selecionado que seja fechado o arquivo
	 * 
	 */
	private void FecharArquivo() 
	{
	    jTextField1.setText("");
	    jTextField2.setText("");
	    jTextField3.setText("");
	    jTextField4.setText("");
	    jTextField5.setText("");
	    jTextField6.setText("");
	    jTextField7.setText("");
	    jTextField8.setText("");
	    jTextField9.setText("");
	    jTextField10.setText("");
	    jTextField1.setEnabled(false);
	    jTextField2.setEnabled(false);
	    jTextField3.setEnabled(false);
	    jTextField4.setEnabled(false);
	    jTextField5.setEnabled(false);
	    jTextField6.setEnabled(false);
	    jTextField7.setEnabled(false);
	    jTextField8.setEnabled(false);
	    jTextField9.setEnabled(false);
	    jTextField10.setEnabled(false);
	    jButton3.setEnabled(false);
	    jButton2.setEnabled(false);
	    jMenuItem2.setEnabled(false);
	    jMenuItem5.setEnabled(false);        
	    jMenuItem1.setEnabled(false);                
	    for(int i=tabelaItens.getModel().getRowCount()-1;i>=0;i--) {
	       ((DefaultTableModel) tabelaItens.getModel()).removeRow(i);
	    }
	}
    
	/**
	 * Metodo main da classe principal
	 * @param args the command line arguments
	 */
	public static void main( String... args) {
	    java.awt.EventQueue.invokeLater(new Runnable() {
	        public void run() {
	            new SiamProposta().setVisible(true);
	        }
	    });
	}
}
