<?php
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
?>
<?php
 /**
  * Página de
  * Data de criação : 13/12/2005

   * @autor Analista    : Diego
   * @autor Programador : Rodrigo Schreiner / Fernando Zank

   Caso de uso : 03.03.12

 */

/*
$Log$
Revision 1.8  2006/07/20 21:11:56  fernando
alteração na padronização dos UC

Revision 1.7  2006/07/19 11:44:01  fernando
Inclusão do  Ajuda.

Revision 1.6  2006/07/06 14:00:33  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:52  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterImportacaoCatalogo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
 $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
 $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("telaPrincipal");
$obForm->setEncType("multipart/form-data");

//Cria objetos ocultos
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obHdnCod = new Hidden;
$obHdnCod->setName ("inCodigo");
$obHdnCod->setValue($inCodigo);

//Cria objetos campo textos
$obTxtDescricaoCatalogo = new TextBox;
$obTxtDescricaoCatalogo->setRotulo("Descrição do Catálogo");
$obTxtDescricaoCatalogo->setTitle ("Informe a descrição do catálogo.Deve ser a mesma descrição do arquivo catalogo.csv.");
$obTxtDescricaoCatalogo->setName  ("stDescricaoCatalogo");
$obTxtDescricaoCatalogo->setNull  (false);

$obTxtDelimitadorColuna = new TextBox;
$obTxtDelimitadorColuna->setRotulo("Delimitador de coluna");
$obTxtDelimitadorColuna->setTitle ("Informe o delimitador de coluna dos arquivos.");
$obTxtDelimitadorColuna->setName  ("stDelimitadorColuna");
$obTxtDelimitadorColuna->setSize  (1);
$obTxtDelimitadorColuna->setValue (";");
$obTxtDelimitadorColuna->setNull  (false);

$obTxtDelimitadorTexto = new TextBox;
$obTxtDelimitadorTexto->setRotulo("Delimitador de texto");
$obTxtDelimitadorTexto->setTitle ("Informe o delimitador de texto dos arquivos.");
$obTxtDelimitadorTexto->setName  ("stDelimitadorTexto");
$obTxtDelimitadorTexto->setSize  (1);
$obTxtDelimitadorTexto->setValue  ("&#34;");
$obTxtDelimitadorTexto->setNull  (false);

//Cria objetos campo upload
$obFilArquivoCatalogo = new FileBox;
$obFilArquivoCatalogo->setRotulo   ("Arquivo Catalogo.csv");
$obFilArquivoCatalogo->setName     ("stArquivoCatalogo");
$obFilArquivoCatalogo->setSize     (40);
$obFilArquivoCatalogo->setTitle    ("Selecione o arquivo catalogo.csv.");
$obFilArquivoCatalogo->setNull     (false);
$obFilArquivoCatalogo->setMaxLength(100);

$obFilArquivoClassificacao = new FileBox;
$obFilArquivoClassificacao->setRotulo   ("Arquivo Classificacao.csv");
$obFilArquivoClassificacao->setName     ("stArquivoClassificacao");
$obFilArquivoClassificacao->setSize     (40);
$obFilArquivoClassificacao->setTitle    ("Selecione o arquivo classificacao.csv.");
$obFilArquivoClassificacao->setNull     (false);
$obFilArquivoClassificacao->setMaxLength(100);

$obFilArquivoItem = new FileBox;
$obFilArquivoItem->setRotulo   ("Arquivo Item.csv");
$obFilArquivoItem->setName     ("stArquivoItem");
$obFilArquivoItem->setSize     (40);
$obFilArquivoItem->setTitle    ("Selecione o arquivo item.csv.");
$obFilArquivoItem->setNull     (false);
$obFilArquivoItem->setMaxLength(100);

$obFilArquivoAtributo = new FileBox;
$obFilArquivoAtributo->setRotulo   ("Arquivo Atributo.csv");
$obFilArquivoAtributo->setName     ("stArquivoAtributo");
$obFilArquivoAtributo->setSize     (40);
$obFilArquivoAtributo->setTitle    ("Selecione o arquivo atributo.csv.");
$obFilArquivoAtributo->setNull     (false);
$obFilArquivoAtributo->setMaxLength(100);

$obFilArquivoItemAtributos = new FileBox;
$obFilArquivoItemAtributos->setRotulo   ("Arquivo ItemAtributos.csv");
$obFilArquivoItemAtributos->setName     ("stArquivoItemAtributos");
$obFilArquivoItemAtributos->setSize     (40);
$obFilArquivoItemAtributos->setTitle    ("Selecione o arquivo itemAtributos.csv.");
$obFilArquivoItemAtributos->setNull     (false);
$obFilArquivoItemAtributos->setMaxLength(100);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
//$obFormulario->setAjuda     ("UC-03.03.12");
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnCtrl);
$obFormulario->addHidden    ($obHdnCod);
$obFormulario->addTitulo    ("Dados dos Arquivos");
$obFormulario->addComponente($obTxtDescricaoCatalogo);
$obFormulario->addComponente($obFilArquivoCatalogo);
$obFormulario->addComponente($obFilArquivoClassificacao);
$obFormulario->addComponente($obFilArquivoItem);
$obFormulario->addComponente($obFilArquivoAtributo);
$obFormulario->addComponente($obFilArquivoItemAtributos);
$obFormulario->addComponente($obTxtDelimitadorColuna);
$obFormulario->addComponente($obTxtDelimitadorTexto);

$obFormulario->OK();
$obFormulario->show();
