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
    * Data de Criação: 04/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26154 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-17 11:42:13 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioApolice.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAgendarManutencao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'montaPlacaIdentificacao':

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $obTxtNumeroPlaca = new TextBox();
            $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
            $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
            $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
            $obTxtNumeroPlaca->setNull( false );
            $obTxtNumeroPlaca->setValue( $_REQUEST['stNumPlaca'] );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtNumeroPlaca );
            $obFormulario->montaInnerHTML();

            $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
        }
        break;
    case 'montaPlacaIdentificacaoFiltro':

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $obTxtNumeroPlaca = new TextBox();
            $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
            $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
            $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
            $obTxtNumeroPlaca->setNull( true );

            $obTipoBuscaNumeroPlaca = new TipoBusca( $obTxtNumeroPlaca );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTipoBuscaNumeroPlaca );
            $obFormulario->montaInnerHTML();

            $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
        }
        break;
    case 'montaAtributos' :
        if ($_REQUEST['stCodClassificacao']) {
            $arClassificacao = explode( '.',$_REQUEST['stCodClassificacao'] );
            list( $_REQUEST['inCodNatureza'], $_REQUEST['inCodGrupo'], $_REQUEST['inCodEspecie'] ) = $arClassificacao;
        }

        if ($_REQUEST['inCodEspecie'] OR $_REQUEST['stCodClassificacao']) {
            $obRCadastroDinamico = new RCadastroDinamico();
            $obRCadastroDinamico->setCodCadastro( 1 );
            $obRCadastroDinamico->obRModulo->setCodModulo( 6 );
            if ($_REQUEST['inCodBem']) {
                $obRCadastroDinamico->setChavePersistenteValores( array( 'cod_bem' => $_REQUEST['inCodBem'], 'cod_especie' => $_REQUEST['inCodEspecie'], 'cod_grupo' => $_REQUEST['inCodGrupo'] ,'cod_natureza' => $_REQUEST['inCodNatureza'] ) );
                $obRCadastroDinamico->setPersistenteAtributos( new TPatrimonioEspecieAtributo );
                $obRCadastroDinamico->setPersistenteValores( new TPatrimonioBemAtributoEspecie );
                $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosAux );
            } else {
                $obRCadastroDinamico->setChavePersistenteValores( array( 'cod_especie' => $_REQUEST['inCodEspecie'], 'cod_grupo' => $_REQUEST['inCodGrupo'] ,'cod_natureza' => $_REQUEST['inCodNatureza'] ) );
                $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosAux );
            }

            //recupera os registros da table patrimonio.especie_atributo que estão ativos
            $obTPatrimonioEspecieAtributo = new TPatrimonioEspecieAtributo();
            $obTPatrimonioEspecieAtributo->setDado( 'cod_modulo', 6 );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_cadastro', 1 );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'true' );
            $obTPatrimonioEspecieAtributo->recuperaEspecieAtributo( $rsAtributosAtivos );

            $arAtivas = array();

            while ( !$rsAtributosAtivos->eof() ) {
                $arAtivas[] = $rsAtributosAtivos->getCampo('cod_atributo');
                $rsAtributosAtivos->proximo();
            }

            $rsAtributos = new RecordSet();

            for ( $i = 0; $i < $rsAtributosAux->getNumLinhas(); $i++ ) {
                if ( in_array( $rsAtributosAux->arElementos[$i]['cod_atributo'], $arAtivas ) ) {
                    $rsAtributos->add( $rsAtributosAux->arElementos[$i] );
                }
            }

            //monta os atributos dinamicos
            $obMontaAtributos = new MontaAtributos;
            $obMontaAtributos->setTitulo     ( "Atributos"  );
            $obMontaAtributos->setName       ( "Atributo_"  );
            $obMontaAtributos->setRecordSet  ( $rsAtributos );
            $obMontaAtributos->recuperaValores();

            if ( $rsAtributos->getNumLinhas() >0 ) {
                $obFormulario = new Formulario();
                $obMontaAtributos->geraFormulario( $obFormulario );
                $obFormulario->montaInnerHTML();

                //passa pela sessão o recordset de atributos para fazer a verificação no PR
                Sessao::write('rsAtributosDinamicos',$rsAtributos);

                $stJs.= "$('spnAtributos').innerHTML = '".$obFormulario->getHTML()."';";
            } else {
                //reseta o transf
                Sessao::remove('rsAtributosDinamicos');
                $stJs.= "$('spnAtributos').innerHTML = '';";
            }
        } else {
            $stJs.= "$('spnAtributos').innerHTML = '';";
        }

        break;
    case 'montaApolice' :
        //monta o span com os dados da apólice
        if ($_REQUEST['stApolice'] == 'sim') {
            //recupera todas as seguradoras
            $obTPatrimonioApolice = new TPatrimonioApolice();
            $obTPatrimonioApolice->recuperaSeguradoras( $rsSeguradoras, 'ORDER BY nom_seguradora' );

            $obSelectSeguradora = new Select();
            $obSelectSeguradora->setName( 'inCodSeguradora' );
            $obSelectSeguradora->setRotulo( 'Seguradora' );
            $obSelectSeguradora->setTitle( 'Seleciona a seguradora.' );
            $obSelectSeguradora->addOption( '','Selecione' );
            $obSelectSeguradora->setCampoId( 'num_seguradora' );
            $obSelectSeguradora->setCampoDesc( 'nom_seguradora' );
            $obSelectSeguradora->preencheCombo( $rsSeguradoras );
            $obSelectSeguradora->obEvento->setOnChange( "montaParametrosGET( 'preencheApolice', 'inCodSeguradora' );" );
            $obSelectSeguradora->setValue( $_REQUEST['inCodSeguradora'] );
            $obSelectSeguradora->setNull( false );

            $obSelectApolice = new Select();
            $obSelectApolice->setName( 'inCodApolice' );
            $obSelectApolice->setId( 'inCodApolice' );
            $obSelectApolice->setRotulo( 'Apólice' );
            $obSelectApolice->setTitle( 'Selecione a apólice.' );
            $obSelectApolice->addOption( '','Selecione' );
            $obSelectApolice->setNull( false );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obSelectSeguradora );
            $obFormulario->addComponente( $obSelectApolice );
            $obFormulario->montaInnerHTML();

            $stJs .= "$('spnApolice').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs .= "$('spnApolice').innerHTML = '';";
        }
        break;
    case 'preencheApolice' :
        $stJs.= "limpaSelect($('inCodApolice'),1);";
        if ($_REQUEST['inCodSeguradora'] != '') {
            $obTPatrimonioApolice = new TPatrimonioApolice();
            $obTPatrimonioApolice->setDado( 'numcgm', $_REQUEST['inCodSeguradora'] );
            $obTPatrimonioApolice->recuperaApoliceSeguradora( $rsApolices );

            $inCount = 1;
            while ( !$rsApolices->eof() ) {
                $stSelected = ( $_REQUEST['inCodApolice'] == $rsApolices->getCampo( 'cod_apolice' ) ) ? 'selected' : '';
                $stJs .= "$('inCodApolice').options[".$inCount."] = new Option( '".$rsApolices->getCampo( 'num_apolice' ).' - '.$rsApolices->getCampo( 'dt_vencimento' )."','".$rsApolices->getCampo( 'cod_apolice' )."', '".$stSelected."' );";
                $inCount++;
                $rsApolices->proximo();
            }

        }
        break;
}

echo $stJs;
