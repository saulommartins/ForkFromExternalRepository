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
 * XML
 *
 * Classe que insere e altera colunas no documento XML das cotações de compra direta
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

import java.io.File;
import java.io.FileOutputStream;
import java.util.ArrayList;
import java.util.List;

import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

/**
 * XML
 * 
 * Classe que insere e altera dados no documento XML das cotações de compra direta
 * 
 * @author luiz
 * @package urbem
 * @import java.io.File
 *
 */
public class XML 
{
	/**
	 * setSaved
	 * 
	 * Seta se o arquivo que esta sendo manipulado já foi salvo
	 * 
	 * @param b
	 */
    void setSaved(boolean b) {
        this._saved = b;
    }
    
   class XMLInvalidoException extends Exception 
   {
      
	   private static final long serialVersionUID = 1L;

	   public XMLInvalidoException() {
          super("O Arquivo XML invalido.");
	   }
   }
   
   private File _file;
   private List<Item> _items = new ArrayList<Item>();
   private Fornecedor _fornecedor;
   private Document _doc;
   private boolean _saved = false;

   /**
    * XML
    * 
    * Gera um novo arquivo XML
    * 
    * @param filename
    * @throws Exception
    */
   public XML(String filename) throws Exception {
      this(new File(filename));
   }

   /**
    * XML
    * 
    * Gera um novo arquivo XML
    * 
    * @param file
    * @throws Exception
    */
   public XML(File file) throws Exception {
      _file = file;
      _doc = DocumentBuilderFactory.newInstance().newDocumentBuilder().parse(_file);
      if (!isValido()) {
         throw new XMLInvalidoException(); 
      }
      preencheFornecedor();
      preencheItems();
   }
  
   /**
    * isValido
    * 
    * Verifica se o arquivo Xml é valido
    * 
    * @return
    */
   private boolean isValido() 
   {
      NodeList nodeList = _doc.getElementsByTagName("compra");
      if (nodeList.item(0) == null) {
         return false;
      }
      return true;
   }

   /**
    * atualizaFornecedor
    * 
    * atualiza as informações do fornecedor que estão no xml
    */
   private void atualizaFornecedor() 
   {
      NodeList nodeList = _doc.getElementsByTagName("fornecedor");
      Element fornecedorElement;
      if (nodeList.item(0) == null) {
          fornecedorElement = _doc.createElement("fornecedor");
          _doc.getElementsByTagName("compra").item(0).appendChild(fornecedorElement);
      }
      else 
         fornecedorElement=(Element)nodeList.item(0);    
      Element element = criaElementoSeNaoExite(fornecedorElement,"cnpj");
      element.setTextContent(_fornecedor.cnpj) ;
      element = criaElementoSeNaoExite(fornecedorElement,"razao_social");
      element.setTextContent(_fornecedor.razaoSocial) ;
      element = criaElementoSeNaoExite(fornecedorElement,"nome_fantasia");
      element.setTextContent(_fornecedor.nomeFantasia) ;
      element = criaElementoSeNaoExite(fornecedorElement,"endereco");
      element.setTextContent(_fornecedor.endereco) ;
      element = criaElementoSeNaoExite(fornecedorElement,"cidade");
      element.setTextContent(_fornecedor.cidade) ;
      element = criaElementoSeNaoExite(fornecedorElement,"estado");      
      element.setTextContent(_fornecedor.estado) ;
      element = criaElementoSeNaoExite(fornecedorElement,"cep");
      element.setTextContent(_fornecedor.cep) ;      
      element = criaElementoSeNaoExite(fornecedorElement,"contato");
      element.setTextContent(_fornecedor.contato) ;
      element = criaElementoSeNaoExite(fornecedorElement,"telefone");
      element.setTextContent(_fornecedor.telefone) ;
      element = criaElementoSeNaoExite(fornecedorElement,"email");
      element.setTextContent(_fornecedor.email) ;
    }

   	/**
   	 * criaElementoSeNaoExite
   	 * 
   	 * Cria elementos novos no xml caso eles não existam
   	 * 
   	 * @param elementoPai
   	 * @param name
   	 * @return
   	 */
    private Element criaElementoSeNaoExite(Element elementoPai, String name) {
      Element element = (Element) elementoPai.getElementsByTagName(name).item(0);
      if (element == null) {
          element = _doc.createElement(name);
          elementoPai.appendChild(element);
      }
      return element;
    }

   /**
    * preencheFornecedor
    * 
    * Apenas preenche as informações do fornecedor 
    * para o Form do sistema de propostas
    */
   private void preencheFornecedor() 
   {
      NodeList nodeList = _doc.getElementsByTagName("fornecedor");
      Element element=(Element)nodeList.item(0);
      _fornecedor = new Fornecedor();
      if (element != null) {
         if (element.getElementsByTagName("cnpj").item(0) != null)
            _fornecedor.cnpj = element.getElementsByTagName("cnpj").item(0).getTextContent();
        if (element.getElementsByTagName("razao_social").item(0) != null)
            _fornecedor.razaoSocial = element.getElementsByTagName("razao_social").item(0).getTextContent();
        if (element.getElementsByTagName("nome_fantasia").item(0) != null)
            _fornecedor.nomeFantasia = element.getElementsByTagName("nome_fantasia").item(0).getTextContent();
        if (element.getElementsByTagName("endereco").item(0) != null)
            _fornecedor.endereco = element.getElementsByTagName("endereco").item(0).getTextContent();
        if (element.getElementsByTagName("cidade").item(0) != null)
            _fornecedor.cidade = element.getElementsByTagName("cidade").item(0).getTextContent();
        if (element.getElementsByTagName("estado").item(0) != null)
            _fornecedor.estado = element.getElementsByTagName("estado").item(0).getTextContent();
        if (element.getElementsByTagName("cep").item(0) != null)
            _fornecedor.cep = element.getElementsByTagName("cep").item(0).getTextContent();         
        if (element.getElementsByTagName("contato").item(0) != null)
            _fornecedor.contato = element.getElementsByTagName("contato").item(0).getTextContent();
        if (element.getElementsByTagName("telefone").item(0) != null)
            _fornecedor.telefone = element.getElementsByTagName("telefone").item(0).getTextContent();
        if (element.getElementsByTagName("email").item(0) != null)
            _fornecedor.email = element.getElementsByTagName("email").item(0).getTextContent();       
      }
   }

   /**
    * preencheItens
    * 
    * Recupera as informações dos itens do xml para preencher 
    * a table do sistema de propostas
    */
   private void preencheItems() {
      NodeList nodeList = _doc.getElementsByTagName("item");
      for(int i=0;i<nodeList.getLength();i++){
         Element element=(Element)nodeList.item(i);
         Item item = new Item();
         item.codigo = Integer.parseInt(element.getElementsByTagName("codigo").item(0).getTextContent());
         item.descricaoResumida = element.getElementsByTagName("descricao_resumida").item(0).getTextContent();
         item.unidade = element.getElementsByTagName("unidade").item(0).getTextContent();         
         if (element.getElementsByTagName("marca").item(0) != null)
            item.marca = element.getElementsByTagName("marca").item(0).getTextContent();
         if (element.getElementsByTagName("data_validade_proposta").item(0) != null)
             item.data_validade_proposta = element.getElementsByTagName("data_validade_proposta").item(0).getTextContent();
         if (element.getElementsByTagName("valor").item(0) != null)
            item.valor = Float.parseFloat(element.getElementsByTagName("valor").item(0).getTextContent());
         _items.add(item);
      }
   }

   /**
    * salvar
    * 
    * salva o arquivo xml
    */
   public void salvar() 
   {
      salvar(_file);
   }

   /**
    * salvar
    *
    * salva o arquivo xml
    * @param file
    */
   public void salvar(File file) 
   {
        _saved = true;
        _file = file;
        atualizaFornecedor();       
        atualizaItens();
        try {
           Transformer transformer = TransformerFactory.newInstance().newTransformer();
           transformer.setOutputProperty(OutputKeys.INDENT, "yes");
           
           transformer.transform(new DOMSource(_doc), new StreamResult(new FileOutputStream(_file)));
        } catch(Exception e) { }
   }

   /**
    * getItems
    * 
    * recupera os itens que estão no xml
    * 
    * @return
    */
   public List<Item> getItems() {
      return _items;
   }

   /**
    * getFornecedores
    * 
    * recupera os dados do fornecedor que esta no xml
    * 
    * @return array
    */
   public Fornecedor getFornecedor() 
   {
      return _fornecedor;
   }

   /**
    * atualizaItens
    * 
    * Atualiza o xml com as novas informações dos itens 
    * setadas no sistema de propostas
    */
   private void atualizaItens() 
   {
      NodeList nodeList = _doc.getElementsByTagName("item");
      for(int i=0;i<nodeList.getLength();i++){
         Element element=(Element)nodeList.item(i);
         Item item = _items.get(i);
         element.getElementsByTagName("codigo").item(0).setTextContent(String.valueOf(item.codigo)) ;
         element.getElementsByTagName("descricao_resumida").item(0).setTextContent(String.valueOf(item.descricaoResumida));
         criaElementoSeNaoExite(element, "marca");
         element.getElementsByTagName("marca").item(0).setTextContent(item.marca);
         criaElementoSeNaoExite(element, "data_validade_proposta");         
         element.getElementsByTagName("data_validade_proposta").item(0).setTextContent(String.valueOf(item.data_validade_proposta));         
         criaElementoSeNaoExite(element, "valor");         
         element.getElementsByTagName("valor").item(0).setTextContent(String.valueOf(item.valor));         
      }
   }
   
   /**
    * isSaved
    * 
    * verifica se o arquivo esta salvo
    * 
    * @return boolean
    */
   public boolean isSaved() 
   {
      return _saved;
   }
   
}
