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
    * Interface de Inclusão/Alteração Função Orçamentária
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.8  2007/05/21 18:56:06  melo
Bug #9229#

Revision 1.7  2006/07/14 18:11:19  leandro.zis
Bug #6376#

Revision 1.6  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoFuncao.class.php"        );
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "Funcao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

/**
    * Instância o OBJETO da regra de negócios ROrcamentoFuncao
*/
$obROrcamentoFuncao         = new ROrcamentoFuncao;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

/**
    * Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// Consulta a configuração para selecionar o GRUPO X
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$stMascara = $obRConfiguracaoOrcamento->getMascDespesa();
$arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);
// Grupo X;
$stMascara = $arMarcara[2];

/**
    * Busca os dados para alteração
*/
if ($stAcao == "alterar") {
    $obROrcamentoFuncao->setCodigoFuncao( $_GET["inCodigoFuncao"] );
    $obROrcamentoFuncao->consultar();
    $inCodigoFuncao = $obROrcamentoFuncao->getCodigoFuncao();
    $stDescricao    = $obROrcamentoFuncao->getDescricao();

    /**
        * Define o objeto LABEL código da natureza jurídica
    */
    $obTxtCodFuncao = new Label;
    $obTxtCodFuncao->setName    ( "inCodigoFuncao" );
    $obTxtCodFuncao->setValue   ( $inCodigoFuncao  );
    $obTxtCodFuncao->setRotulo  ( "Codigo" );
}

/**
    * Define o objeto da CODIGO FUNCAO
*/
$obHdnCodigo = new Hidden;
$obHdnCodigo->setName  ( "inCodigoFuncao" );
$obHdnCodigo->setValue ( $inCodigoFuncao  );

/**
    * Instancia o formulário
*/
$obForm = new Form;
$obForm->setAction  ( $pgProc  );
$obForm->setTarget  ( "oculto" ); //oculto - telaPrincipal

/**
    * Define o objeto da ação stAcao
*/
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

/**
    * Define o objeto de controle
*/
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

if ($stAcao =="incluir") {
    $obTxtCodFuncao = new TextBox;
    $obTxtCodFuncao->setName     ( "inNumeroFuncao"   );
    $obTxtCodFuncao->setValue    ( $inNumeroFuncao    );
    $obTxtCodFuncao->setRotulo   ( "Código" );
    $obTxtCodFuncao->setSize     ( strlen($stMascara) );
    $obTxtCodFuncao->setMaxLength( strlen($stMascara) );
    $obTxtCodFuncao->setNull     ( false );
    $obTxtCodFuncao->setInteiro  ( true );
    $obTxtCodFuncao->setTitle    ( "Informe o código da função." );
    $obTxtCodFuncao->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");
}

/**
    * Define o objeto TEXTBOX descrição
*/
$obTxtDescFuncao = new TextBox;
$obTxtDescFuncao->setName       ( "stDescricao" );
$obTxtDescFuncao->setValue      ( $stDescricao  );
$obTxtDescFuncao->setRotulo     ( "Descrição"   );
$obTxtDescFuncao->setTitle      ( "Informe a descrição."   );
$obTxtDescFuncao->setSize       ( 80 );
$obTxtDescFuncao->setMaxLength  ( 80 );
$obTxtDescFuncao->setNull       ( false );

/**
    * Define o objeto HIDDEN para controle de paginacão
*/
$obHdnPg = new Hidden;
$obHdnPg->setName  ( "pg" );
$obHdnPg->setValue ( $_GET["pg"] );

/**
    * Define o objeto HIDDEN para controle de paginacão
*/
$obHdnPos = new Hidden;
$obHdnPos->setName  ( "pos" );
$obHdnPos->setValue ( $_GET["pos"] );

/**
    * Criacão do formulário
*/
$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-02.01.03" );
$obFormulario->addForm   ( $obForm       );
$obFormulario->addHidden ( $obHdnCtrl    );
$obFormulario->addHidden ( $obHdnAcao    );
$obFormulario->addHidden ( $obHdnPg      );
$obFormulario->addHidden ( $obHdnPos     );
$obFormulario->addHidden ( $obHdnCodigo  );

$obFormulario->addTitulo( "Dados para Função Orçamentária" );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obTxtCodFuncao  );
} else {
    $obFormulario->addComponente( $obTxtCodFuncao  );
}
$obFormulario->addComponente( $obTxtDescFuncao );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
