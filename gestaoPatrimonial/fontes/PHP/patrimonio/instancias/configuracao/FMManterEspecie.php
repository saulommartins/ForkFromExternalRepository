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
    * Data de Criação: 06/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28252 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 13:51:49 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.5  2007/10/17 13:27:12  hboaventura
correção dos arquivos

Revision 1.4  2007/10/05 12:59:27  hboaventura
inclusão dos arquivos

Revision 1.3  2007/09/27 12:57:13  hboaventura
adicionando arquivos

Revision 1.2  2007/09/18 15:36:50  hboaventura
Adicionando ao repositório

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php");
include_once( CAM_GP_PAT_COMPONENTES."ISelectGrupo.class.php");
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");

$stPrograma = "ManterEspecie";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//se a acao for alterar, recupera os dados da base
if ($stAcao == 'alterar') {
    $obTPatrimonioEspecie = new TPatrimonioEspecie();

    $obTPatrimonioEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
    $obTPatrimonioEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
    $obTPatrimonioEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );

    $obTPatrimonioEspecie->recuperaEspecie( $rsEspecie );
    $inCodNatureza = $rsEspecie->getCampo( 'cod_natureza' );
    $inCodGrupo = $rsEspecie->getCampo( 'cod_grupo' );
    $stNomEspecie = $rsEspecie->getCampo( 'nom_especie' );

    //cria um objeto hidden para passar o valor do cod_especie
    $obHdnCodEspecie = new Hidden();
    $obHdnCodEspecie->setName 	  ( 'inCodEspecie' );
    $obHdnCodEspecie->setValue    ( $rsEspecie->getCampo('cod_especie') );

    //cria um objeto hidden para passar o valor do cod_natureza
    $obHdnCodGrupo = new Hidden();
    $obHdnCodGrupo->setName 	  ( 'inCodGrupo' );
    $obHdnCodGrupo->setValue    ( $rsEspecie->getCampo('cod_grupo') );

    //cria um objeto hidden para passar o valor do cod_natureza
    $obHdnCodNatureza = new Hidden();
    $obHdnCodNatureza->setName 	  ( 'inCodNatureza' );
    $obHdnCodNatureza->setValue    ( $rsEspecie->getCampo('cod_natureza') );

    //cria um label para a natureza
    $obLblNatureza = new Label();
    $obLblNatureza->setRotulo( 'Natureza' );
    $obLblNatureza->setValue( $rsEspecie->getCampo('cod_natureza').' - '.$rsEspecie->getCampo('nom_natureza')  );

    //cria um label para a natureza
    $obLblGrupo = new Label();
    $obLblGrupo->setRotulo( 'Grupo' );
    $obLblGrupo->setValue( $rsEspecie->getCampo('cod_grupo').' - '.$rsEspecie->getCampo('nom_grupo')  );

    //cri aum label para demonstrar o código da espécie
    $obLblCodEspecie = new Label();
    $obLblCodEspecie->setRotulo( 'Código da Espécie' );
    $obLblCodEspecie->setValue( $rsEspecie->getCampo('cod_especie') );

}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria o componente ISelectGrupo
$obISelectGrupo = new ISelectGrupo( $obForm );
$obISelectGrupo->obISelectNatureza->setValue( $inCodNatureza );
$obISelectGrupo->obSelectGrupo->setValue( $inCodGrupo );

//cria o textbox da descrição da situacao do bom
$obTxtDescricaoEspecie = new TextBox();
$obTxtDescricaoEspecie->setId    ( 'stDescricaoEspecie' );
$obTxtDescricaoEspecie->setName  ( 'stDescricaoEspecie' );
$obTxtDescricaoEspecie->setRotulo( 'Descrição da Espécie' );
$obTxtDescricaoEspecie->setTitle ( 'Informe a descrição da espécie do bem.' );
$obTxtDescricaoEspecie->setSize  ( 50 );
$obTxtDescricaoEspecie->setMaxLength( 60 );
$obTxtDescricaoEspecie->setNull  ( false );
$obTxtDescricaoEspecie->setValue ( $stNomEspecie );

$rsAtributosSelecionados = new RecordSet();
$rsAtributosDisponiveis = new RecordSet();
$arInativos = array();

$obRCadastroDinamico = new RCadastroDinamico();
if ($stAcao == 'alterar') {
    $obRCadastroDinamico->setChavePersistenteValores( array( 'cod_especie' => $_REQUEST['inCodEspecie'], 'cod_grupo' => $_REQUEST['inCodGrupo'] ,'cod_natureza' => $_REQUEST['inCodNatureza'] ) );
    $obRCadastroDinamico->setCodCadastro( 1 );
    $obRCadastroDinamico->obRModulo->setCodModulo( 6 );
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDisponiveisAux );

    //recupera os atributos ativos no sistema
    $obTPatrimonioEspecieAtributo = new TPatrimonioEspecieAtributo();
    $obTPatrimonioEspecieAtributo->setDado( 'cod_especie' , $_REQUEST['inCodEspecie'] );
    $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo'   , $_REQUEST['inCodGrupo']   );
    $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza']);
    $obTPatrimonioEspecieAtributo->setDado( 'ativo'       , 'true'                    );
    $obTPatrimonioEspecieAtributo->recuperaEspecieAtributo( $rsAtributosAtivos        );

    $arAtivos = array();
    while ( !$rsAtributosAtivos->eof() ) {
        $arAtivos[] = $rsAtributosAtivos->getCampo( 'cod_atributo' );
        $rsAtributosAtivos->proximo();
    }

    //verifica se o atributo selecionado não está inativado, se estiver, coloca ele como disponível
    while ( !$rsAtributosDisponiveisAux->eof() ) {
        if ( in_array( $rsAtributosDisponiveisAux->getCampo('cod_atributo'), $arAtivos ) ) {
            $rsAtributosSelecionados->add( $rsAtributosDisponiveisAux->arElementos[ $rsAtributosDisponiveisAux->getCorrente() -1 ] );
        } else {
            $rsAtributosDisponiveis->add( $rsAtributosDisponiveisAux->arElementos[ $rsAtributosDisponiveisAux->getCorrente() -1 ] );
        }
        $rsAtributosDisponiveisAux->proximo();
    }
} else {
    $obRCadastroDinamico->setPersistenteAtributos( new TAdministracaoAtributoDinamico );
    $obRCadastroDinamico->setCodCadastro( 1 );
    $obRCadastroDinamico->obRModulo->setCodModulo( 6 );
    $obRCadastroDinamico->recuperaAtributos( $rsAtributosDisponiveis );
}

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName   ( 'inCodAtributos');
$obCmbAtributos->setTitle  ( 'Selecione os atributos que serão selecionados na entrada de estoque.');
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );
$obCmbAtributos->setTitle  ( "Selecione os atributos de entrada." );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1 ('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1   ('cod_atributo');
$obCmbAtributos->setCampoDesc1 ('nom_atributo');
$obCmbAtributos->SetRecord1    ( $rsAtributosDisponiveis );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2 ('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2   ('cod_atributo');
$obCmbAtributos->setCampoDesc2 ('nom_atributo');
$obCmbAtributos->SetRecord2    ( $rsAtributosSelecionados );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.05');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados da Situação do Bem" );
//inclui no formulario o objeto hidden que foi criado previamente
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodEspecie );
    $obFormulario->addHidden( $obHdnCodGrupo );
    $obFormulario->addHidden( $obHdnCodNatureza );

    $obFormulario->addComponente( $obLblNatureza );
    $obFormulario->addComponente( $obLblGrupo );
    $obFormulario->addComponente( $obLblCodEspecie );
} else {
    $obISelectGrupo->geraFormulario( $obFormulario );
}
$obFormulario->addComponente( $obTxtDescricaoEspecie );
if ( $rsAtributosDisponiveis->getNumLinhas() > 0 OR $rsAtributosSelecionados->getNumLinhas() > 0 ) {
    $obFormulario->addTitulo( "Atributos" );
    $obFormulario->addComponente( $obCmbAtributos );
}
if ($stAcao == 'alterar') {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&pos=".$_REQUEST['pos']."&pg=".$_REQUEST['pg'] );
} else {
    $obFormulario->OK();
}
$obFormulario->show();
