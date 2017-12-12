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
 * Classe para imprimir Autentica��es localmente
 *
 * @author Analista: Lucas Leusin
 * @author Desenvolvedor: Cleisson Barboza
 * @author Desenvolvedor: Jos� Eduardo Porto
 *
 * Caso de uso: uc-2.4.15
 *
 */
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;

public class Autenticacao extends JApplet implements ActionListener{

    private ImprimeAutenticacao imp;

    private String[] texto;
    private String impressora;

    private int cont = 0;

    private JButton button;
    private JComboBox comboBox;

    private Container container;

    public void setTexto (String[] texto) {

        this.texto = texto;
    }
    public void setImpressora (String impressora) {

        this.impressora = impressora;
    }

    public String[] getTexto () {

        return this.texto;
    }
    public String getImpressora () {

        return this.impressora;
    }

    public void init(){

        imp = new ImprimeAutenticacao();
        
        container = getContentPane();
        container.setLayout(null);

        comboBox = new JComboBox(imp.listarImpressoras());
        comboBox.setSelectedItem(imp.consultarImpressoraDefault());
        comboBox.setBounds(0,0,150,20);

        button = new JButton("Imprimir");
        button.setBackground(new Color(239,239,239));
        button.addActionListener(this);
        button.setBounds(151,0,120,20);

        container.add(comboBox);
        container.add(button);

        container.setBackground(new Color(220,220,220));

    }

    public void actionPerformed(ActionEvent actionEvent) {        

        if ( actionEvent.getSource() == button) {

            if (cont < this.getTexto().length) {

                imprime(this.getTexto()[cont]);
            }else {

                cont = 0;

                imprime(this.getTexto()[cont]);
            }

            cont++;
        }
    }

    public final void imprime(String dadosImpressao) {

        byte[] bytes = new String(dadosImpressao).getBytes();

        imp.imprimir(bytes,(String)this.comboBox.getSelectedItem() );

    }

}
