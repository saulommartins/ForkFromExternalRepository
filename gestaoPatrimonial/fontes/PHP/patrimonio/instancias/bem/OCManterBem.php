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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterBem.php 66372 2016-08-19 19:06:35Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php";
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioApolice.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBemPlanoAnalitica.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioDepreciacao.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioReavaliacao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'montaPlacaIdentificacao':
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado('exercicio'  , Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado('cod_modulo' , 6);
        $obTAdministracaoConfiguracao->pegaConfiguracao($boPlacaAlfa, 'placa_alfanumerica');
    
        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $obTxtNumeroPlaca = new TextBox;
            $obTxtNumeroPlaca->setId     ('stNumeroPlaca');
            $obTxtNumeroPlaca->setName   ('stNumeroPlaca');
            $obTxtNumeroPlaca->setTitle  ('Informe o número da placa do bem.');
            $obTxtNumeroPlaca->setRotulo ('Número da Placa');
    
            if ($_REQUEST['stAcao'] != 'consultar') {
                 $obTxtNumeroPlaca->setNull( false );
            } else {
                $obTxtNumeroPlaca->setNull( true );
            }
    
            $obTPatrimonioBem = new TPatrimonioBem;
    
            # Defini se o campo será inteiro ou alfanumérico e recupera o ultimo valor.
            if ($boPlacaAlfa == 'false') {
                $obTxtNumeroPlaca->setInteiro(true);
                $obTPatrimonioBem->recuperaMaxNumPlacaNumerico( $rsNumPlaca );
            } else {
                $obTxtNumeroPlaca->setCaracteresAceitos("[a-zA-Z0-9\-]");
                $obTPatrimonioBem->recuperaMaxNumPlacaAlfanumerico( $rsNumPlaca );
    
                # Incrementa o numero da placa e depois atribui ao componente.
            }
    
            $maxNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
    
            # Sugere o nro máximo da placa e incrementa.
            $obTxtNumeroPlaca->setValue( ++$maxNumeroPlaca );
            $obTxtNumeroPlaca->obEvento->setOnChange("montaParametrosGET('verificaIntervalo','stNumeroPlaca, inQtdeLote');");
    
            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtNumeroPlaca );
            $obFormulario->montaInnerHTML();
    
            $stJs.= "jQuery('#spnNumeroPlaca').html('".$obFormulario->getHTML()."');";
        } else {
            $stJs.= "jQuery('#spnNumeroPlaca').html('');";
        }

    break;

    case 'montaPlacaIdentificacaoLote':
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAdministracaoConfiguracao->setDado( 'cod_modulo', 6 );
        $obTAdministracaoConfiguracao->pegaConfiguracao( $boPlacaAlfa, 'placa_alfanumerica' );
    
        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $obTxtNumeroPlaca = new TextBox();
            $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
            $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
            $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
            $obTxtNumeroPlaca->setId( 'stNumeroPlaca' );
    
            if ($boPlacaAlfa == 'false') {
                $obTxtNumeroPlaca->setInteiro (true);
            } else {
                $obTxtNumeroPlaca->setCaracteresAceitos( "[a-zA-Z0-9\-]" );
            }
    
            if ($_REQUEST['stAcao'] == 'consultar') {
                $obTxtNumeroPlaca->setNull( true );
            } else {
                $obTxtNumeroPlaca->setNull( false );
            }
    
            $obTxtNumeroPlaca->setValue( $_REQUEST['stNumPlaca'] );
    
            if ($_REQUEST['recuperaMax'] == 'true') {
                $obTPatrimonioBem = new TPatrimonioBem();
    
                if ($boPlacaAlfa == 'true') {
                    $obTPatrimonioBem->recuperaMaxNumPlacaAlfanumerico($rsNumPlaca);
                    $maxNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
                } else {
                    $obTPatrimonioBem->recuperaMaxNumPlacaNumerico($rsNumPlaca);
    
                    if ( $rsNumPlaca->getNumLinhas() <=0 ) {
                        $inMaiorNumeroPlaca = 0;
                    } else {
                        $inMaiorNumeroPlaca = $rsNumPlaca->getCampo('num_placa');
                    }
    
                    $maxNumeroPlaca = $inMaiorNumeroPlaca;
                }
    
                $maxNumeroPlaca++;
    
                // Incrementa o numero da placa e depois atribui ao componente!!
                $obTxtNumeroPlaca->setValue( $maxNumeroPlaca );
            }
    
            $obTxtNumeroPlaca->obEvento->setOnChange( "montaParametrosGET( 'verificaIntervalo','stNumeroPlaca,inQtdeLote' );" );
    
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
    
            if ( $rsAtributos->getNumLinhas() > 0 ) {
                $obFormulario = new Formulario();
                $obMontaAtributos->geraFormulario( $obFormulario );
                $obFormulario->montaInnerHTML();
    
                //passa pela sessão o recordset de atributos para fazer a verificação no PR
                Sessao::write('rsAtributosDinamicos',$rsAtributosDinamicos);
    
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

    case 'verificaIntervalo':
        if ($_REQUEST['stNumeroPlaca'] != '' && $_REQUEST['inQtdeLote'] != '' && $_REQUEST['inQtdeLote'] > 0) {
            $obTPatrimonioBem = new TPatrimonioBem();
            $arNumPlaca = array();
            $numeroPlaca = $_REQUEST['stNumeroPlaca'];
            // monta um array com os números das placas possíveis de acordo com a
            // quantidade informada
            // A consulta é feita de forma fragmentada para não estourar o buffer do banco para a clausula IN
            $inUltimo = 0;
            for ($i=0; $i < $_REQUEST['inQtdeLote']; $i++) {
                $arNumPlaca[] = "'".($numeroPlaca++)."'";
                if ( ($i % 1000) == 0 and $i > 0) {
                    $stFiltro = " WHERE num_placa IN (".implode("," ,$arNumPlaca).")";
                    $arNumPlaca = array();
                    $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );
                    if ( $rsBem->getNumLinhas() > 0 ) {
                        break;
                    }
    
                }
            }
            if ( count($arNumPlaca) > 0 ) {
                $stFiltro = " WHERE num_placa IN (".implode("," ,$arNumPlaca).")";
                $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );
            }
    
            if ( $rsBem->getNumLinhas() >= 0 ) {
                $intervalo = ($_REQUEST['inQtdeLote'] - 1) + $_REQUEST['stNumeroPlaca'];
                $stJs.= "alertaAviso('Já existem bens com placas no intervalo selecionado (".$_REQUEST['stNumeroPlaca']." - ".$intervalo.")!','form','erro','".Sessao::getId()."');";
                break;
            }
    
        }
    break;

    case 'preencheComboEntidade' :
        $stJs.= "limpaSelect($('inCodEntidade'),1);";
        if ($_REQUEST['stExercicio']) {
        //cria o filtro
        $stFiltro = " AND E.exercicio = '".$_REQUEST['stExercicio']."' ";
        //recupera todos as entidades para o exercicio
    
        $obTOrcamentoEntidade = new TOrcamentoEntidade();
        $obTOrcamentoEntidade->recuperaRelacionamento( $rsEntidades, $stFiltro, ' ORDER BY cod_entidade ' );
            if ( $rsEntidades->getNumLinhas() > 0 ) {
                $inCount = 1;
                while ( !$rsEntidades->eof() ) {
                    if(( $_REQUEST['inCodEntidade'] == $rsEntidades->getCampo('cod_entidade') )){
                        $stSelected = 'true';
                    }else{
                        $stSelected = 'false';
                    }
                    $stJs .= "jq('#".inCodEntidade."').addOption('".$rsEntidades->getCampo('cod_entidade')."','".$rsEntidades->getCampo('cod_entidade')." - ".$rsEntidades->getCampo('nom_cgm')."',".$stSelected.");";
                    $rsEntidades->proximo();
                    $inCount++;
                }
            } else {
                $stJs.= "$('stExercicio').value = ''; ";
                $stJs.= "alertaAviso('Exercício sem entidades cadastradas.','form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case 'montaListaAtributos':
        unset($obAtributo);
        //padrao para identificacao do atributo "cod_modulo,cod_cadastro,cod_atributo"
        $arAtributo = explode(',', $_REQUEST['stAtributo']);
    
        $rsAtributo = new RecordSet();
        $stFiltro = " AND ad.cod_modulo = ".$arAtributo[0]."
                  AND ad.cod_cadastro = ".$arAtributo[1]."
                  AND ad.cod_atributo = ".$arAtributo[2]."
                  ";

        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
        $obTAdministracaoAtributoDinamico->recuperaRelacionamento($rsAtributo, $stFiltro);
    
        while (!$rsAtributo->eof()) {
            switch ($rsAtributo->getCampo('cod_tipo')) {
                case 1: //númerico
                    $obAtributo = new Inteiro();
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );//pensar em como setar o name e o id
                    $obAtributo->setNull( true );
                break;
                case 2: //texto
                    $obAtributo = new TextBox();
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                    $obAtributo->setNull( true );
                    $obAtributo->setMaxLength( 100 );
                    $obAtributo->setSize( 70 );
                break;
                case 3: //Lista
                    $stFiltro = " WHERE bem_atributo_especie.cod_modulo = ".$arAtributo[0]."
                            AND bem_atributo_especie.cod_cadastro = ".$arAtributo[1]."
                            AND bem_atributo_especie.cod_atributo = ".$arAtributo[2];
                    require_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php");
                    $obTPatrimonioBemAtributoEspecie = new TPatrimonioBemAtributoEspecie();
                    $obTPatrimonioBemAtributoEspecie->recuperaAtributosValores($rsAtributosValoresLista, $stFiltro);
    
                    $obAtributo = new Select;
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                    $obAtributo->setValue( "" );
                    $obAtributo->setStyle( "width: 200px" );
                    $obAtributo->addOption( "", "Selecione" );
                    while (!$rsAtributosValoresLista->eof()) {
                        $obAtributo->addOption( $rsAtributosValoresLista->getCampo('cod_valor'), $rsAtributosValoresLista->getCampo('valor_padrao') );
                        $rsAtributosValoresLista->proximo();
                    }
                    $obAtributo->setNull( true );
                break;
                case 4: //Lista múltipla
                break;
                case 5: //Data
                    //instancia um componente periodicidade
                    $obAtributo = new Periodicidade();
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setIdComponente( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                    $obAtributo->setName( '' );
                    $obAtributo->setNull( true );
                    $obAtributo->setExercicio ( Sessao::getExercicio() );
                break;
                case 6: //númerico(*, 2)
                    $obAtributo = new Moeda();
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                    $obAtributo->setNull( true );
                break;
                case 7: //texto longo
                    $obAtributo = new TextBox();
                    $obAtributo->setRotulo( $rsAtributo->getCampo('nom_atributo') );
                    $obAtributo->setTitle( $rsAtributo->getCampo('ajuda') );
                    $obAtributo->setName( "atributos[".$rsAtributo->getCampo('cod_modulo').",".$rsAtributo->getCampo('cod_cadastro').",".$rsAtributo->getCampo('cod_atributo')."]" );
                    $obAtributo->setNull( true );
                    $obAtributo->setMaxLength( '' );
                    $obAtributo->setSize( 60 );
                break;
            }
            $rsAtributo->proximo();
        }
    
        $obFormulario = new Formulario();
        $obFormulario->addComponente( $obAtributo );
        $obFormulario->montaInnerHTML();
    
        $stJs .= "$('stAtributo').value = '';";
        $stJs .= "var html = $('spnListaAtributos').innerHTML;";
        $stJs .= "$('spnListaAtributos').innerHTML = html + '".$obFormulario->getHTML()."';";
    break;

    case 'minimoQuotaAnual':
        if($_REQUEST['flQuotaDepreciacaoAnual']!=''){
            $flQuotaDepreciacaoAnual = str_replace(',','.',str_replace('.','',$_REQUEST['flQuotaDepreciacaoAnual']));
            $vlMinimo = '4.00';

            if($flQuotaDepreciacaoAnual<$vlMinimo){
                $stMsgErro  = 'Percentual mínimo do campo Quota de Depreciação Anual é '.$vlMinimo = number_format($vlMinimo, 2 , ',' , '.' ).'%;';
                $stMsgErro .= ' Conforme base legal: IN SRF nº 162/1998 e IN SRF nº 130/1999.';

                $stJs  = "alertaAviso('".$stMsgErro."','form','erro','".Sessao::getId()."');";
                $stJs .= "jQuery('#flQuotaDepreciacaoAnual').val('');";
            }
        }
    break;

    case 'montaDepreciacao':
        include_once CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php';
    
        if ($_REQUEST['boDepreciavel'] == 'true') {
            $obNumQuotaDepreciacaoAnual = new Numerico;
            $obNumQuotaDepreciacaoAnual->setName    ('flQuotaDepreciacaoAnual');
            $obNumQuotaDepreciacaoAnual->setId      ('flQuotaDepreciacaoAnual');
            $obNumQuotaDepreciacaoAnual->setRotulo  ('Quota de Depreciação Anual');
            $obNumQuotaDepreciacaoAnual->setTitle   ('Valor percentual de depreciação anual do bem.');
            $obNumQuotaDepreciacaoAnual->setMaxValue('100');
            $obNumQuotaDepreciacaoAnual->setDecimais(2);
            $obNumQuotaDepreciacaoAnual->setMaxLength(5);
            $obNumQuotaDepreciacaoAnual->setValue(str_replace('.',',', $_REQUEST['inVlQuotaDepreciacaoAnual']));
            $obNumQuotaDepreciacaoAnual->obEvento->setOnChange("montaParametrosGET( 'minimoQuotaAnual', 'dtAquisicao,flQuotaDepreciacaoAnual' );");
    
            $obLblPercent = new Label;
            $obLblPercent->setValue('%');
    
            $obForm = new Form;
            $obForm->setAction ($pgProc);
            $obForm->setTarget ("oculto");
    
            //cria um busca inner para retornar uma conta contábil
            $obBscContaContabil = new BuscaInner;
            $obBscContaContabil->setRotulo               ( "Conta Contábil" );
            $obBscContaContabil->setTitle                ( "Informe a conta do plano de contas.");
            $obBscContaContabil->setId                   ( "stDescricaoConta" );
            $obBscContaContabil->obCampoCod->setName     ( "inCodContaAnalitica" );
            $obBscContaContabil->obCampoCod->setSize     ( 10 );
            $obBscContaContabil->obCampoCod->setAlign    ( "left" );
            $obBscContaContabil->setValoresBusca	     ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaSinteticaAtivoPermanente");
            $obBscContaContabil->setFuncaoBusca 	     ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaAnalitica','stDescricaoConta','contaSinteticaAtivoPermanente','".Sessao::getId()."','800','550');" );
            $obBscContaContabil->setNull                 ( true );
            $obBscContaContabil->setValue	             ( $_REQUEST['stNomePlanoConta'] );
            $obBscContaContabil->obCampoCod->setValue    ( $_REQUEST['inPlanoContaAnalitica'] );
    
            $obRdDepreciacaoAceleradaSim = new Radio;
            $obRdDepreciacaoAceleradaSim->setName   ('boDepreciacaoAcelerada');
            $obRdDepreciacaoAceleradaSim->setId     ('boDepreciacaoAcelerada');
            $obRdDepreciacaoAceleradaSim->setRotulo ('Depreciação Acelerada');
            $obRdDepreciacaoAceleradaSim->setLabel  ('Sim');
            $obRdDepreciacaoAceleradaSim->setTitle  ('Determina se o bem terá depreciação acelerada ou não.');
            $obRdDepreciacaoAceleradaSim->setValue  ('true');
    
            $obRdDepreciacaoAceleradaNao = new Radio;
            $obRdDepreciacaoAceleradaNao->setName   ('boDepreciacaoAcelerada');
            $obRdDepreciacaoAceleradaNao->setRotulo ('Depreciação Acelerada');
            $obRdDepreciacaoAceleradaNao->setLabel  ('Não');
            $obRdDepreciacaoAceleradaNao->setTitle  ('Determina se o bem terá depreciação acelerada ou não.');
            $obRdDepreciacaoAceleradaNao->setValue  ('false');
            $obRdDepreciacaoAceleradaNao->obEvento->setOnChange("montaParametrosGET( 'montaDepreciacaoAcelerada', 'boDepreciacaoAcelerada' );");
    
            if ($_REQUEST['flDepreciacaoAcelerada'] > 0) {
                $obRdDepreciacaoAceleradaSim->setChecked(true);
                $obRdDepreciacaoAceleradaSim->obEvento->setOnChange("montaParametrosGET( 'montaDepreciacaoAcelerada', 'boDepreciacaoAcelerada,flDepreciacaoAnualAcelerada' );");
            } else {
                $obRdDepreciacaoAceleradaNao->setChecked(true);
                $obRdDepreciacaoAceleradaSim->obEvento->setOnChange("montaParametrosGET( 'montaDepreciacaoAcelerada', 'boDepreciacaoAcelerada' );");
            }
    
            $obSpnDepreciacaoAcelerada = new Span;
            $obSpnDepreciacaoAcelerada->setId('stDepreciacaoAcelerada');
    
            $obSpnListaDepreciacao = new Span;
            $obSpnListaDepreciacao->setId('stSpnListaDepreciacao');
    
            $obSpnListaDepreciacaoTableTree = new Span;
            $obSpnListaDepreciacaoTableTree->setId('stSpnListaDepreciacaoTableTree');
    
            if ($_REQUEST["boDepreciavel"] == 'true' && $_REQUEST["stAcao"] === 'alterar' && $_REQUEST["inVlBem"] != '') {
                $obBtnExcluirDepreciacao = new Button;
                $obBtnExcluirDepreciacao->setValue             ( "Excluir última depreciação" );
                $obBtnExcluirDepreciacao->setId                ( "btnExcluirDepreciacao" );
                $obBtnExcluirDepreciacao->obEvento->setOnClick ( "montaParametrosGET('excluiDepreciacaoLista')");
            }
    
            $obFormulario = new Formulario;
            $obFormulario->addForm      ( $obForm );
            $obFormulario->agrupaComponentes(array($obNumQuotaDepreciacaoAnual,$obLblPercent));
            $obFormulario->addComponente    ( $obBscContaContabil );
    
            $obFormulario->agrupaComponentes(array($obRdDepreciacaoAceleradaSim,$obRdDepreciacaoAceleradaNao));
            $obFormulario->addSpan          ($obSpnDepreciacaoAcelerada);
            $obFormulario->addSpan          ($obSpnListaDepreciacao);
            $obFormulario->addSpan          ($obSpnListaDepreciacaoTableTree);
           
            if ($_REQUEST["stAcao"] === 'alterar') {
                Sessao::write('arDepreciacao',array());
    
                $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
                $obTPatrimonioDepreciacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioDepreciacao->recuperaDepreciacao( $rsDepreciacao );
                            
                if ($rsDepreciacao->getNumLinhas() > 0) {
                    $stJs .= " montaParametrosGET( 'montaListaDepreciacoes', 'stLimpar, inCodBem');";
                }
            }
    
            $obFormulario->montaInnerHTML();
            $stJs .= "jQuery('#stDepreciacao').html('".$obFormulario->getHTML()."');\n";
        } else {
            Sessao::write('arDepreciacao',array());
    
            $obFormulario = new Formulario;
    
            $obSpnDepreciacaoAcelerada = new Span;
            $obSpnDepreciacaoAcelerada->setId('stDepreciacaoAcelerada');
    
            $obSpnListaDepreciacao = new Span;
            $obSpnListaDepreciacao->setId('stSpnListaDepreciacao');
    
            $obSpnListaDepreciacaoTableTree = new Span;
            $obSpnListaDepreciacaoTableTree->setId('stSpnListaDepreciacaoTableTree');
    
            $obFormulario->addSpan ($obSpnDepreciacaoAcelerada);
            $obFormulario->addSpan ($obSpnListaDepreciacao);
            $obFormulario->addSpan ($obSpnListaDepreciacaoTableTree);
            $obFormulario->montaInnerHTML ();
    
            $stJs  = "jQuery('#stDepreciacao').html('".$obFormulario->getHTML()."');\n";
            $stJs .= " montaParametrosGET( 'montaListaDepreciacoes', 'stLimpar, inCodBem');";
        }
    break;

    case 'montaDepreciacaoAcelerada':
        $obNumQuotaDepreciacaoAcelerada = new Numerico;
        $obNumQuotaDepreciacaoAcelerada->setName    ('flQuotaDepreciacaoAcelerada');
        $obNumQuotaDepreciacaoAcelerada->setRotulo  ('Quota de Depreciação Acelerada Anual');
        $obNumQuotaDepreciacaoAcelerada->setTitle   ('Valor percentual de depreciação acelerada do bem.');
        $obNumQuotaDepreciacaoAcelerada->setMaxValue('100');
        $obNumQuotaDepreciacaoAcelerada->setDecimais(2);
        $obNumQuotaDepreciacaoAcelerada->setMaxLength(5);
        $obNumQuotaDepreciacaoAcelerada->setObrigatorio(true);
    
        if ($_REQUEST['flDepreciacaoAcelerada'] != '') {
            $obNumQuotaDepreciacaoAcelerada->setValue($_REQUEST['flDepreciacaoAcelerada'] );
        }
    
        $obLblPercent = new Label;
        $obLblPercent->setValue('%');
    
        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes(array($obNumQuotaDepreciacaoAcelerada,$obLblPercent));
        $obFormulario->montaInnerHTML();
    
        if ($_REQUEST['boDepreciacaoAcelerada'] == 'true' || $_REQUEST['flDepreciacaoAcelerada'] > 0) {
            $stJs = "jQuery('#stDepreciacaoAcelerada').html('".$obFormulario->getHTML()."')";
        } else {
            $stJs = "jQuery('#stDepreciacaoAcelerada').html('')";
        }
    break;

    case 'montaListaReavaliacoes':    
        $obErro = new Erro;
        $arReavaliacao = Sessao::read('arReavaliacao');
    
        if ($_REQUEST['stIncluirReavaliacao'] == 'Incluir') {
            $dataAtual = date("Y").date("m").date("d");
    
            list($ano, $mes, $dia) = explode("/", $_REQUEST['dtAquisicao']);
            $dtAquisicao = $dia.$mes.$ano;
    
            list($ano, $mes, $dia) = explode("/", $_REQUEST['dtIncorporacao']);
            $dtIncorporacao = $dia.$mes.$ano;
    
            list($ano, $mes, $dia) = explode("/", $_REQUEST['dtReavaliacao']);
            $dtReavaliacao = $dia.$mes.$ano;
    
            list($ano, $mes, $dia) = explode("/", Sessao::read('dtUltimaReavaliacao'));
            $dtUltimaReavaliacao = $dia.$mes.$ano;
    
            $dtUltimaDepreciacao = Sessao::read('stDepreciacaoCompetencia');
    
            $inCodReavaliacao       = 0;
            $inCodBem     		= $_REQUEST['inCodBem'];
            $inVidaUtilReavaliacao  = $_REQUEST['inVidaUtilReavaliacao'];
            $flValorBemReavaliacao  = floatval(str_replace(",",".",str_replace(".", "", $_REQUEST['flValorBemReavaliacao'])));
            $stMotivoReavaliacao    = $_REQUEST['stMotivoReavaliacao'];

            if ($dtReavaliacao === '') {
                $obErro->setDescricao('Campo Data da Reavaliação inválido!');
            } elseif (substr($dtReavaliacao,0,6) <= $dtUltimaDepreciacao) {
                $obErro->setDescricao('Campo Data da Reavaliação deve ser maior que a competência da última depreciação!');
            } elseif (intval($dtUltimaReavaliacao) > intval($dtReavaliacao)) {
                $obErro->setDescricao('Campo Data da Reavaliação deve ser maior que a última Reavaliação informada!');
            } elseif ($dtAquisicao > $dtReavaliacao) {
                $obErro->setDescricao('Campo Data da Reavaliação deve ser maior que a Data de Aquisição do Bem!');
            } elseif ($dtIncorporacao > $dtReavaliacao) {
                $obErro->setDescricao('Campo Data da Reavaliação deve ser maior que a Data de Incorporação do Bem!');
            } elseif ($inVidaUtilReavaliacao === '') {
                $obErro->setDescricao('O campo Vida Útil do Bem deve ter valor maior que zero!');
            } elseif ($flValorBemReavaliacao <= 0.00) {
                $obErro->setDescricao('O campo Valor da Reavaliação deve ter valor maior que zero!');
            } elseif ($stMotivoReavaliacao === '') {
                $obErro->setDescricao('O campo Motivo deve ser preenchido!');
            } elseif ($dtReavaliacao > $dataAtual) {
                $obErro->setDescricao('Campo Data da Reavaliação não pode ser maior que a data atual!');
            }

            if ($obErro->ocorreu()) {
                $stJs  =  "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
            }

            if (!$obErro->ocorreu()) {
                $arReavaliacao[] = array(
                    'inId'                          => count($arReavaliacao) + 1,
                    'inCodReavaliacao'              => $inCodReavaliacao,
                    'inCodBem'                      => $inCodBem,
                    'dtReavaliacao'                 => $_REQUEST['dtReavaliacao'],
                    'inVidaUtilReavaliacao'         => $inVidaUtilReavaliacao,
                    'flValorBemReavaliacao'         => $flValorBemReavaliacao,
                    'stMotivoReavaliacao'           => $stMotivoReavaliacao,
                    'inserir'                       => 'true',
                    'situacao'                      => 'Pendente',
                );

                Sessao::write('dtUltimaReavaliacao',$_REQUEST['dtReavaliacao']);
                Sessao::write('arReavaliacao',$arReavaliacao);
            }
        } elseif ($_REQUEST['stLimparReavaliacao'] == 'Limpar') {
    
        } else {
            Sessao::remove('dtUltimaReavaliacao');
            Sessao::remove('arReavaliacao');
    
            if (trim($_REQUEST['inCodBem']) != '') {
                $rsReavaliacao = new RecordSet;
                $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
                $obTPatrimonioReavaliacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
                $obTPatrimonioReavaliacao->recuperaRelacionamento( $rsReavaliacao );

                $arReavaliacao = array();

                while (!$rsReavaliacao->eof()) {
                    $inCodReavaliacao      = $rsReavaliacao->getCampo('cod_reavaliacao');
                    $inCodBem              = $rsReavaliacao->getCampo('cod_bem');
                    $dtReavaliacao         = $rsReavaliacao->getCampo('dt_reavaliacao');
                    $inVidaUtilReavaliacao = $rsReavaliacao->getCampo('vida_util');
                    $flValorBemReavaliacao = $rsReavaliacao->getCampo('vl_reavaliacao');
                    $stMotivoReavaliacao   = $rsReavaliacao->getCampo('motivo');
                    $stSituacao            = $rsReavaliacao->getCampo('situacao');
                    $arReavaliacao[] = array(
                        'inId'                  => count($arReavaliacao) + 1,
                        'inCodReavaliacao'      => $inCodReavaliacao,
                        'inCodBem'              => $inCodBem,
                        'dtReavaliacao'         => $dtReavaliacao,
                        'inVidaUtilReavaliacao' => $inVidaUtilReavaliacao,
                        'flValorBemReavaliacao' => $flValorBemReavaliacao,
                        'stMotivoReavaliacao'   => $stMotivoReavaliacao,
                        'inserir'               => 'false',
                        'situacao'              => $stSituacao,
                    );

                    Sessao::write('arReavaliacao',$arReavaliacao);
                    Sessao::write('dtUltimaReavaliacao',$dtReavaliacao);
                    $rsReavaliacao->proximo();
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $rsReavaliacao = new RecordSet;
            $rsReavaliacao->preenche($arReavaliacao);
            $rsReavaliacao->addFormatacao('flValorBemReavaliacao' , 'NUMERIC_BR');

            $obTable = new Table();
            $obTable->setRecordset( $rsReavaliacao);
            $obTable->setSummary( 'Lista de Reavaliações' );
            $obTable->Head->addCabecalho('Data Reavaliação'    ,10);
            $obTable->Head->addCabecalho('Vida Útil'           , 8);
            $obTable->Head->addCabecalho('Valor da Reavaliação',15);
            $obTable->Head->addCabecalho('Motivo'              ,50);
            $obTable->Head->addCabecalho('Lançamento Contábil' ,10);
            $obTable->Body->addCampo( 'dtReavaliacao'        , 'C' );
            $obTable->Body->addCampo( 'inVidaUtilReavaliacao', 'C' );
            $obTable->Body->addCampo( 'flValorBemReavaliacao', 'C' );
            $obTable->Body->addCampo( 'stMotivoReavaliacao'  , 'E' );
            $obTable->Body->addCampo( 'situacao'             , 'C' );
            $obTable->Body->addAcao( 'excluir', "executaFuncaoAjax( 'excluiReavaliacaoLista', '&inId=%s&inCodReavaliacao=%s&inCodBem=%s&dtReavaliacao=%s');", array( 'inId','inCodReavaliacao','inCodBem','dtReavaliacao' ) );
            $obTable->montaHTML( true );
            $stHTML = $obTable->getHTML();
            $stJs  = "jQuery('#stSpnListaReavaliacao').html('".$stHTML."');";
            $stJs  .= "jQuery('#dtReavaliacao').val('');";
            $stJs  .= "jQuery('#inVidaUtilReavaliacao').val('');";
            $stJs  .= "jQuery('#flValorBemReavaliacao').val('');";
            $stJs  .= "jQuery('#stMotivoReavaliacao').val('');";
        }
    break;

    case 'montaListaDepreciacoes':
    $arDepreciacao = Sessao::read('arDepreciacao');

    if ($_REQUEST['inCodBem'] != '') {
        $stJs   = "jQuery('#stSpnListaDepreciacaoTableTree').html('&nbsp;');";
        $stJs  .= "jQuery('#stSpnListaDepreciacao').html('&nbsp;');";

        $arCompetencia = array(
           '01' => 'Janeiro',
           '02' => 'Fevereiro',
           '03' => 'Março',
           '04' => 'Abril',
           '05' => 'Maio',
           '06' => 'Junho',
           '07' => 'Julho',
           '08' => 'Agosto',
           '09' => 'Setembro',
           '10' => 'Outubro',
           '11' => 'Novembro',
           '12' => 'Dezembro',
        );

        $rsDepreciacao = new RecordSet;
        $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
        $obTPatrimonioDepreciacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioDepreciacao->recuperaDepreciacao( $rsDepreciacao );

        $arDtReavaliacao = explode('/',Sessao::read('dtUltimaReavaliacao'));
        $stDtReavaliacao = $arDtReavaliacao[2].$arDtReavaliacao[1];

        if ($stDtReavaliacao != '') {
            if ($rsDepreciacao->getNumLinhas() > 0) {
                foreach ($rsDepreciacao->getElementos() as $i => $val) {
                    if ($val['competencia'] >= $stDtReavaliacao) {
                        $arDepreciacaoAux[] = $val;
                    }
                }

                $rsDepreciacao->preenche($arDepreciacaoAux);
            }
        }
        
        $rsSaldoBem = new RecordSet;
        $rsBem = new RecordSet;
        $obTPatrimonioBem = new TPatrimonioBem();
        $obTPatrimonioBem->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioBem->recuperaSaldoBem( $rsSaldoBem );
        $obTPatrimonioBem->recuperaRelacionamento( $rsBem );

        $numLinhas = 0;

        while (!$rsDepreciacao->eof()) {
            if ($numLinhas == 0) {
                $flQuotaDepreciacaoAceleradaPrimeiroMes = $rsDepreciacao->getCampo('quota_utilizada');
            }
            $flQuotaDepreciacaoAcelerada  = $flQuotaDepreciacaoAceleradaPrimeiroMes + ($rsDepreciacao->getCampo('quota_utilizada') * 11);

            $numLinhas  += 1;
            $rsDepreciacao->proximo();
        }

        $rsDepreciacao->setPrimeiroElemento();

        while (!$rsDepreciacao->eof() ) {
            $inCodDepreciacao   = $rsDepreciacao->getCampo('cod_depreciacao');
            $inCodBem           = $rsDepreciacao->getCampo('cod_bem');
            $timestamp          = $rsDepreciacao->getCampo('timestamp');
            $inCodPlano         = $rsDepreciacao->getCampo('cod_plano');
            $inExercicio        = $rsDepreciacao->getCampo('exercicio');
            $flValorDepreciacao = $rsDepreciacao->getCampo('vl_depreciado');
            $dt_depreciacao     = $rsDepreciacao->getCampo('dt_depreciacao');
            $stCompetencia      = $arCompetencia[substr($rsDepreciacao->getCampo('competencia'),4,5)].'/'.substr($rsDepreciacao->getCampo('competencia'),0,4);
            $stCompetenciaBase  = $rsDepreciacao->getCampo('competencia');
            
            Sessao::write('stDepreciacaoCompetencia',substr($rsDepreciacao->getCampo('competencia'),0,2).substr($rsDepreciacao->getCampo('competencia'),2,5));
                        
            $stMotivo                   = $rsDepreciacao->getCampo('motivo');
            $stDepreciacaoAcelerada     = $rsDepreciacao->getCampo('acelerada') == 't' ? 'Sim' : 'Não';
            $inVlAtualizadoDepreciacao  = $rsSaldoBem->getCampo('inVlAtualizadoDepreciacao');
            $inVlDepreciacaoAcumulada   = $rsSaldoBem->getCampo('inVlDepreciacaoAcumulada');
            $flIndiceDepreciacaoMes     = $rsDepreciacao->getCampo('quota_utilizada');
            $flQuotaDepreciacaoAnual    = $rsBem->getCampo('quota_depreciacao_anual');

            $inUltimaCompetencia = ltrim(substr($rsDepreciacao->getCampo('competencia'),2,5), '0');
            $inTipoCompetencia   = $rsDepreciacao->getCampo('tipocompetencia');

            $rsReavaliacaoTemp = new RecordSet;
            $rsReavaliacaoTemp->preenche($rsDepreciacao);

            if ($rsDepreciacao->getCampo('acelerada') == 'f') {
                $flQuotaDepreciacaoAcelerada  = "0,00";
            }

            $arDepreciacao[] = array(
                'inId'                        => count($arDepreciacao) + 1,
                'inCodDepreciacao'            => $inCodDepreciacao,
                'inCodBem'                    => $inCodBem,
                'inCodPlano'                  => $inCodPlano,
                'timestamp'                   => $timestamp,
                'inCompetencia'               => $inCompetencia,
                'inExercicio'                 => $inExercicio,
                'stCompetencia'               => $stCompetencia,
                'stCompetenciaBase'           => $stCompetenciaBase,
                'stMotivo'                    => $stMotivo,
                'stDepreciacaoAcelerada'      => $stDepreciacaoAcelerada,
                'flQuotaDepreciacaoAnual'     => $flQuotaDepreciacaoAnual,
                'flQuotaDepreciacaoAcelerada' => $flQuotaDepreciacaoAcelerada,
                'flIndiceDepreciacaoMes'      => $flIndiceDepreciacaoMes,
                'flValorDepreciacao'          => $flValorDepreciacao,
                'inserir'                     => 'false',
                'tipoCompetencia'             => $inTipoCompetencia,
            );

            Sessao::write('arDepreciacao',$arDepreciacao);
            $rsDepreciacao->proximo();
        }
    }

    if (count($arDepreciacao) > 0) {
        if ($inTipoCompetencia == 2 || $inTipoCompetencia == 3 || $inTipoCompetencia == 4 || $inTipoCompetencia == 6 || Sessao::read('tipoCompetencia') == "6") {
            $inTipoCompetencia = $inTipoCompetencia == ""? Sessao::read('tipoCompetencia') : $inTipoCompetencia;
            $arCompetencias = montaCompetencia($inTipoCompetencia,$inUltimaCompetencia);
            $rsCompetencias = new RecordSet;
            $rsCompetencias->preenche($arCompetencias);
            $obTable = new TableTree();
            $obTable->setRecordset( $rsCompetencias );
            $obTable->setSummary('Lista de Depreciações');
            $obTable->setConditional( true , "#ddd" );
            $obTable->setArquivo( 'OCManterBem.php');
            $obTable->setParametros( array("inId","tipoCompetencia") );
            $obTable->setComplementoParametros ( "stCtrl=detalhaDepreciacao" );
            $obTable->Head->addCabecalho('Competência',100);
            $obTable->Body->addCampo( '[descricao]', 'E' );

            $obTable->montaHTML(true);
            $stHTML = $obTable->getHtml();

            $stJs .= "jQuery('#stSpnListaDepreciacaoTableTree').html('&nbsp;');         \n";
            $stJs .= "jQuery('#stSpnListaDepreciacaoTableTree').html('".$stHTML."');    \n";
        } else {
            $rsDepreciacao = new RecordSet;
            $rsDepreciacao->preenche($arDepreciacao);
            $rsDepreciacao->addFormatacao('flValorDepreciacao' , 'NUMERIC_BR');
            $rsDepreciacao->addFormatacao('flIndiceDepreciacaoMes' , 'NUMERIC_BR');
            $rsDepreciacao->addFormatacao('flQuotaDepreciacaoAcelerada' , 'NUMERIC_BR');

            $obTable = new Table();
            $obTable->setRecordset( $rsDepreciacao );
            $obTable->setSummary( 'Lista de Depreciações' );
            $obTable->Head->addCabecalho('Competência',10);
            $obTable->Head->addCabecalho('Motivo',50);
            $obTable->Head->addCabecalho('Valor Depreciação',10);
            $obTable->Head->addCabecalho('Quota Depreciação Mensal',10);
            $obTable->Head->addCabecalho('Depreciação Acelerada',10);
            $obTable->Head->addCabecalho('Quota Depreciação Acelerada',15);
            $obTable->Body->addCampo( 'stCompetencia', 'E' );
            $obTable->Body->addCampo( 'stMotivo', 'E' );
            $obTable->Body->addCampo( 'flValorDepreciacao', 'C' );
            $obTable->Body->addCampo( 'flIndiceDepreciacaoMes', 'C' );
            $obTable->Body->addCampo( 'stDepreciacaoAcelerada', 'C' );
            $obTable->Body->addCampo( 'flQuotaDepreciacaoAcelerada', 'C' );
            $obTable->montaHTML( true );
            $stHTML = $obTable->getHTML();

            $stJs  .= "jQuery('#stSpnListaDepreciacao').html('".$stHTML."');";
        }
    } else {
        $stJs  .= "jQuery('#stSpnListaDepreciacaoTableTree').html('');";
        $stJs  .= "jQuery('#stSpnListaDepreciacao').html('');";
    }

    break;
 
    case 'excluiReavaliacaoLista':
        $obErro = new Erro;
        if ($strdtReavaliacao <= $stCompetenciaBase) {
            //a função excluiReavaliacaoLista(); não deve ser excutada, tem que aparecer uma mensagem dizendo
            //que possui Depreciação com competencia maior ou igual.
        }
        $arReavaliacao = Sessao::read('arReavaliacao');
        $arReavaliacaoTmpExcluir = Sessao::read('arReavaliacaoExcluir');

        $rsDepreciacao = new RecordSet;
        $obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();
        $obTPatrimonioDepreciacao->setDado( 'cod_bem', $_REQUEST['inCodBem'] );
        $obTPatrimonioDepreciacao->recuperaDepreciacao( $rsDepreciacao );
        
        if (count($arDepreciacao) == 0) {
            if (!is_array($arReavaliacaoTmpExcluir)) {
                $arReavaliacaoTmpExcluir = array();
            }
    
            $arReavaliacaoTmp = array();
    
            foreach ($arReavaliacao AS $arDados) {
                if ($arDados['inId'] != $_REQUEST['inId']) {
                    $arReavaliacaoTmp[] = $arDados;
                    Sessao::write('dtUltimaReavaliacao',$arDados['dtReavaliacao']);
                    Sessao::write('stDepreciacaoCompetencia',$arDados['competencia']);
                } elseif ($arDados['inserir'] == 'false') {
                    $strdtReavaliacao = substr($arDados['dtReavaliacao'], 3);
                    $strdtReavaliacao = str_replace('/', '',$strdtReavaliacao);
                    
                    if ($rsDepreciacao->getNumLinhas() < 1) {
                        $arReavaliacaoTmpExcluir[] = $arDados;
                    } else {
                        foreach ($rsDepreciacao->arElementos AS $arDeprec) {
                            if ($arDeprec['competencia'] < $strdtReavaliacao) {
                                $arReavaliacaoTmpExcluir[] = $arDados;
                            } else {
                                $arReavaliacaoTmp[] = $arDados;
                                $obErro->setDescricao('A reavaliação não poderá ser removida, pois existem depreciações com data posterior ou igual.');
                            }
                        }
                    }
                }
            }
    
            if (count($arReavaliacaoTmp) == 0) {
                Sessao::remove('dtUltimaReavaliacao');
            }
    
            Sessao::write('arReavaliacao',$arReavaliacaoTmp);
            Sessao::write('arReavaliacaoExcluir',$arReavaliacaoTmpExcluir);
    
            if ($obErro->ocorreu()) {
                $stJs  =  "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
            } else {
                $stJs = "montaParametrosGET('montaListaReavaliacoes','stLimparReavaliacao');";
            }
        } else {
            echo 'erro';
        }
    break;

    case 'excluiDepreciacaoLista':
        $arDepreciacao = Sessao::read('arDepreciacao');
        $arDepreciacaoTmpExcluir = Sessao::read('arDepreciacaoExcluir');

        $inUltimoId = count($arDepreciacao);

        if (!is_array($arDepreciacaoTmpExcluir)) {
            $arDepreciacaoTmpExcluir = array();
        }

        $arDepreciacaoTmp = array();

        foreach ($arDepreciacao AS $arDados) {
            if (($arDados['inserir'] === 'false') && ($arDados['inId'] == $inUltimoId)) {
                $arDepreciacaoTmpExcluir[] = $arDados;
            } else {
                Sessao::write('stDepreciacaoCompetencia',$arDados['competencia']);
                Sessao::write('tipoCompetencia',$arDados['tipoCompetencia']);
                $arDepreciacaoTmp[] = $arDados;
            }
        }

        Sessao::write('arDepreciacao', $arDepreciacaoTmp);
        Sessao::write('arDepreciacaoExcluir', $arDepreciacaoTmpExcluir);

        $stJs = "montaParametrosGET( 'montaListaDepreciacoes','stLimpar');";
    break;

    case 'detalhaDepreciacao':
        $stJs = detalhaDepreciacao($_REQUEST['inId'],$_REQUEST['tipoCompetencia']);
    break;

    case "MontaUnidade":
        $stJs .= "limpaSelect(f.inCodUnidade,0); \n";
        $stJs .= "jq('#inCodUnidadeTxt').value = ''; \n";
        $stJs .= "jq('#inCodUnidade').append( new Option('Selecione','', 'selected')) ;\n";

        if ($_REQUEST["inCodOrgao"]) {
            include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
            $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio() );
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                $stJs .= "jq('#inCodUnidade').append( new Option('".$rsCombo->getCampo("nom_unidade")."','".$rsCombo->getCampo("num_unidade")."' )); \n";
                $rsCombo->proximo();
            }
        } 
        if($_REQUEST["inCodUnidadeTxt"] !=""){
            $stJs.= "jQuery('#inCodUnidade').val(".$_REQUEST["inCodUnidadeTxt"].") ";
        }
    break;

    case "montaObra":
        include_once TTGO.'TTGOObras.class.php';
        include_once TTGO.'TTGOPatrimonioBemObra.class.php';

        $obTTGOObras = new TTGOObras;
        $obTTGOObras->recuperaTodos($rsObra);

        $obCmbObra = new Select;
        $obCmbObra->setTitle     ( "Selecione a Obra"        );
        $obCmbObra->setName      ( "inCodObra"               );
        $obCmbObra->setId        ( "inCodObra"               );
        $obCmbObra->setRotulo    ( "Bens imóveis / Obra"     );
        $obCmbObra->addOption    ( '', 'Selecione'           );
        $obCmbObra->setCampoId   ( "[ano_obra]|[cod_obra]"   );
        $obCmbObra->setCampoDesc ( "[ano_obra] - [cod_obra]" );
        $obCmbObra->preencheCombo( $rsObra                   );

        if(isset($_REQUEST['inCodBem'])) {
            $obTTGOPatrimonioBemObra = new TTGOPatrimonioBemObra;
            $obTTGOPatrimonioBemObra->setDado('cod_bem', $_REQUEST['inCodBem']);
            $obTTGOPatrimonioBemObra->recuperaPorChave($rsPatrimonioBemObra);

            $obCmbObra->setValue($rsPatrimonioBemObra->getCampo('ano_obra').'|'.$rsPatrimonioBemObra->getCampo('cod_obra'));
        }

        $obFormulario = new Formulario;
        $obFormulario->addTitulo	( 'Obra' );
        $obFormulario->addComponente( $obCmbObra );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs .= "document.getElementById('spnListaObra').innerHTML = '".$stHtml."'; \n";
    break;
}

/**
    * Função que retorna uma Lista de Depreciações, quebradas por competência
    * Não é aplicada para os tipos de competências: Mensal e Anual.
    *
*/
function detalhaDepreciacao($inId,$tipoCompetencia)
{
    $arDepreciacao =Sessao::read('arDepreciacao');

    # Utilizado para montagem de intervalos do novo array.
    $inIndiceInicial = ((($inId - 1) * $tipoCompetencia) + 1);
    $inIndiceFinal = ($inId * $tipoCompetencia);

    $rsDepreciacao = new RecordSet;
    $rsDepreciacao->preenche($arDepreciacao);

    while (!$rsDepreciacao->eof()) {
        if (ltrim(substr($rsDepreciacao->getCampo('stCompetenciaBase'),0,2),'0') >= $inIndiceInicial  && $rsDepreciacao->getCampo('inId') <= $inIndiceFinal) {
            if (ltrim(substr($rsDepreciacao->getCampo('stCompetenciaBase'),0,2),'0') <= $inIndiceFinal) {
                $stCompetencia = $rsDepreciacao->getCampo('stCompetencia');
                $stMotivo = $rsDepreciacao->getCampo('stMotivo');
                $inCodDepreciacao = $rsDepreciacao->getCampo('inCodDepreciacao');
                $inCodPlano = $rsDepreciacao->getCampo('inCodPlano');
                $flValorDepreciacao = $rsDepreciacao->getCampo('flValorDepreciacao');
                $flIndiceDepreciacaoMes = $rsDepreciacao->getCampo('flIndiceDepreciacaoMes');
                $stDepreciacaoAcelerada	= $rsDepreciacao->getCampo('stDepreciacaoAcelerada') == 't' ? 'Sim' : 'Não';
                $flQuotaDepreciacaoAcelerada = $rsDepreciacao->getCampo('flQuotaDepreciacaoAcelerada');

                $arDepreciacaoN[]=array(
                    'inId'                          => count($arDepreciacaoN) + 1,
                    'stCompetencia'                 => $stCompetencia,
                    'inCodDepreciacao'              => $inCodDepreciacao,
                    'stMotivo'                      => $stMotivo,
                    'inCodPlano'                    => $inCodPlano,
                    'flValorDepreciacao'            => $flValorDepreciacao,
                    'flIndiceDepreciacaoMes'        => $flIndiceDepreciacaoMes,
                    'stDepreciacaoAcelerada'        => $stDepreciacaoAcelerada,
                    'flQuotaDepreciacaoAcelerada'   => $flQuotaDepreciacaoAcelerada,
                    'inserir'                       => 'false',
                    'tipoCompetencia'               => $tipoCompetencia,
                    );
                $inIndiceInicial++;
            }
            }
        $rsDepreciacao->proximo();
    }

    $rsDepreciacao = new RecordSet;
    if (is_array($arDepreciacaoN)) {
        $rsDepreciacao->preenche($arDepreciacaoN);
    }

    $stJs = montaSpamDetalheDepreciacao( $rsDepreciacao );

    return $stJs;
}

function montaSpamDetalheDepreciacao($rsDados)
{
    $stHTML = montaHTMLConsulta($rsDados);

    return $stHTML;
}

function montaHTMLConsulta($rsDados)
{
    $obTable = new Table();
    $obTable->setRecordset( $rsDados );
    $obTable->setSummary( 'Lista de Depreciações' );
    $obTable->Head->addCabecalho('Competência',10);
    $obTable->Head->addCabecalho('Motivo',50);
    $obTable->Head->addCabecalho('Conta Analítica',10);
    $obTable->Head->addCabecalho('Valor Depreciação',10);
    $obTable->Head->addCabecalho('Quota Depreciação Mensal',10);
    $obTable->Head->addCabecalho('Depreciação Acelerada',10);
    $obTable->Head->addCabecalho('Quota Depreciação Acelerada',15);
    $obTable->Body->addCampo( 'stCompetencia', 'E' );
    $obTable->Body->addCampo( 'stMotivo', 'E' );
    $obTable->Body->addCampo( 'inCodPlano', 'E' );
    $obTable->Body->addCampo( 'flValorDepreciacao', 'C' );
    $obTable->Body->addCampo( 'flIndiceDepreciacaoMes', 'C' );
    $obTable->Body->addCampo( 'stDepreciacaoAcelerada', 'C' );
    $obTable->Body->addCampo( 'flQuotaDepreciacaoAcelerada', 'D' );
    $obTable->montaHTML( true );
    $stHTML = $obTable->getHTML();

    return $stHTML;
}

/**
    * Função que retorna uma Lista de Depreciações, quebradas por competência
    * Não é aplicada para os tipos de competências: Mensal e Anual.
    *
*/
function montaCompetencia($inTipoCompetencia,$inUltimaCompetencia)
{
    $arCompetenciaDepreciacao = array(
        0 => false,
        2 => 'Bimestre ',
        3 => 'Trimestre ',
        4 => 'Quadrimestre ',
        6 => 'Semestre '
    );

    $competencias = 12 / $inTipoCompetencia;
    $competencia = 0;

    $arCompetencia = array();

    for ($counter = 1; $counter <= $competencias; $counter += 1) { {
            $arCompetencia[] = array(
                'inId'            => count($arCompetencia) + 1,
                'tipoCompetencia' => $inTipoCompetencia,
                'descricao'       => $arCompetenciaDepreciacao[$inTipoCompetencia].$counter,
            );
        }
        $competencia += $inTipoCompetencia;
    }

    return $arCompetencia;
}

    echo $stJs;
