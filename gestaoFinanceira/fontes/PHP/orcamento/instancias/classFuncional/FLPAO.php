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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.10  2007/05/21 18:55:47  melo
Bug #9229#

Revision 1.9  2006/07/14 18:11:19  leandro.zis
Bug #6376#

Revision 1.8  2006/07/10 17:16:22  andre.almeida
Correções na paginação.

Revision 1.7  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "PAO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

// Consulta a configuração para selecionar o GRUPO F
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$stMascara = $obRConfiguracaoOrcamento->getMascDespesa();
$arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

// Grupo F;
$stMascara = $arMarcara[5];

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnMascara = new Hidden;
$obHdnMascara->setName  ( "stMascara" );
$obHdnMascara->setValue ( $stMascara  );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodPrograma = new TextBox;
$obTxtCodPrograma->setName     ( "inNumeroProjeto" );
$obTxtCodPrograma->setValue    ( $inNumeroProjeto );
$obTxtCodPrograma->setRotulo   ( "Código" );
$obTxtCodPrograma->setSize     ( strlen($stMascara) );
$obTxtCodPrograma->setMaxLength( strlen($stMascara) );
$obTxtCodPrograma->setNull     ( true );
$obTxtCodPrograma->setTitle    ( 'Informe o código.' );
$obTxtCodPrograma->setInteiro  ( true );
$obTxtCodPrograma->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDescPrograma = new TextBox;
$obTxtDescPrograma->setName     ( "stDescricao" );
$obTxtDescPrograma->setRotulo   ( "Descrição" );
$obTxtDescPrograma->setSize     ( 80 );
$obTxtDescPrograma->setMaxLength( 80 );
$obTxtDescPrograma->setNull     ( true );
$obTxtDescPrograma->setTitle    ( 'Informe a descrição.' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda( "UC-02.01.03"       );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnMascara           );

$obFormulario->addTitulo( "Dados para Filtro"     );
$obFormulario->addComponente( $obTxtCodPrograma   );
$obFormulario->addComponente( $obTxtDescPrograma  );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
