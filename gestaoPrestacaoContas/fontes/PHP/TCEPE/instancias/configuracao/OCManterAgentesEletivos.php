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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira
    *
    $Id: OCManterAgentesEletivos.php 60109 2014-09-30 18:14:20Z michel $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPERelacionarAgenteEletivo.class.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php";
include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";

$stPrograma = "ManterAgentesEletivos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function montaListaCargos($stAcao)
{
    $obTPessoalCargo = new TPessoalCargo();
    $arTipoRemuneracao = array(1=>'Subsídio', 2=>'Representação');
    $arTipoNorma = array(1=>'Lei', 2=>'Resolução', 3=>'Outra');

    if ($stAcao == "mostrar") {
        if (count(Sessao::read('arAgentes'))>0) {
            $arAgentesSessao = Sessao::read('arAgentes');

            for ($inCount = 0 ; $inCount < count($arAgentesSessao);$inCount++) {
                $arElementos[$inCount] = $arAgentesSessao[$inCount];
                $arElementos[$inCount]['descricao_remuneracao'] = $arTipoRemuneracao[$arAgentesSessao[$inCount]['cod_tipo_remuneracao']];
                $arElementos[$inCount]['descricao_norma']       = $arTipoNorma[$arAgentesSessao[$inCount]['cod_tipo_norma']];
                
                foreach($arElementos[$inCount]['cargos'] as $key => $value){
                    unset($rsListaCargo);
                    $stFiltro = "WHERE PC.cod_cargo=".$value;
                    $obTPessoalCargo->listarCargos($rsListaCargo, $stFiltro," ORDER BY descricao");
    
                    while (!$rsListaCargo->eof()) {
                        $arElementos[$inCount]['cargos'][$key] = $arElementos[$inCount]['cargos'][$key]." - ".$rsListaCargo->getCampo("descricao");
                        $rsListaCargo->proximo();
                    }
                }
            }

            $rsAgentes = new RecordSet;
            $rsAgentes->preenche ( $arElementos );
            $rsAgentes->setPrimeiroElemento();

            $obTableTree = new TableTree;
            $obTableTree->setArquivo        ( 'OCManterAgentesEletivos.php'     );
            $obTableTree->setParametros     ( array("cod_tipo")                 );
            $obTableTree->setComplementoParametros( "stCtrl=detalharLista"      );
            $obTableTree->setRecordset      ( $rsAgentes                        );
            $obTableTree->setSummary        ( 'Lista de Agentes Eletivos'       );
            $obTableTree->setConditional    ( true                              );
            $obTableTree->Head->addCabecalho( 'Tipo de Remuneração',20          );
            $obTableTree->Head->addCabecalho( 'Tipo de Norma',20                );
            $obTableTree->Head->addCabecalho( 'Norma',35                        );
            $obTableTree->Body->addCampo( "[cod_tipo_remuneracao] - [descricao_remuneracao]",'E' );
            $obTableTree->Body->addCampo( "[cod_tipo_norma] - [descricao_norma]"            ,'E' );
            $obTableTree->Body->addCampo( "[stNorma]"                                       ,'E' );
            $obTableTree->Body->addAcao ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirAgenteLista','cod_tipo') );
            $obTableTree->montaHTML     ( true );
            
            $stHTML = $obTableTree->getHtml();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";
        } else {
            $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
        }

    } else { //Incluir
        $arAgentesSessao = Sessao::read('arAgentes');

        for ($inCount = 0 ; $inCount < count($arAgentesSessao);$inCount++) {
            $arElementos[$inCount] = $arAgentesSessao[$inCount];
            $arElementos[$inCount]['descricao_remuneracao'] = $arTipoRemuneracao[$arAgentesSessao[$inCount]['cod_tipo_remuneracao']];
            $arElementos[$inCount]['descricao_norma']       = $arTipoNorma[$arAgentesSessao[$inCount]['cod_tipo_norma']];
            
            foreach($arElementos[$inCount]['cargos'] as $key => $value){
                unset($rsListaCargo);
                $stFiltro = "WHERE PC.cod_cargo=".$value;
                $obTPessoalCargo->listarCargos($rsListaCargo, $stFiltro," ORDER BY descricao");

                while (!$rsListaCargo->eof()) {
                    $arElementos[$inCount]['cargos'][$key] = $arElementos[$inCount]['cargos'][$key]." - ".$rsListaCargo->getCampo("descricao");
                    $rsListaCargo->proximo();
                }
            }
        }

        $rsAgentes = new RecordSet;
        $rsAgentes->preenche ( $arElementos );
        $rsAgentes->setPrimeiroElemento();

        $obTableTree = new TableTree;
        $obTableTree->setArquivo        ( 'OCManterAgentesEletivos.php' );
        $obTableTree->setParametros     ( array("cod_tipo")             );
        $obTableTree->setComplementoParametros( "stCtrl=detalharLista"  );
        $obTableTree->setRecordset      ( $rsAgentes                    );
        $obTableTree->setSummary        ( 'Lista de Agentes Eletivos'   );
        $obTableTree->setConditional    ( true                          );
        $obTableTree->Head->addCabecalho( 'Tipo de Remuneração',20      );
        $obTableTree->Head->addCabecalho( 'Tipo de Norma',20            );
        $obTableTree->Head->addCabecalho( 'Norma',35                        );
        $obTableTree->Body->addCampo( "[cod_tipo_remuneracao] - [descricao_remuneracao]",'E' );
        $obTableTree->Body->addCampo( "[cod_tipo_norma] - [descricao_norma]"            ,'E' );
        $obTableTree->Body->addCampo( "[stNorma]"                                       ,'E' );
        $obTableTree->Body->addAcao ( 'excluir','executaFuncaoAjax(\'%s\',\'&inVlTipo=%s\')',array('excluirAgenteLista','cod_tipo') );
        $obTableTree->montaHTML     ( true );
        
        $stHTML = $obTableTree->getHtml();

        $stHTML = str_replace( "\n"     ,""     ,$stHTML );
        $stHTML = str_replace( chr(13)  ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  "     ,""     ,$stHTML );
        $stHTML = str_replace( "'"      ,"\\'"  ,$stHTML );
        $stHTML = str_replace( "\\\\'"  ,"\\'"  ,$stHTML );

        $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";
    }

    return $stJs;
}

function preencherDetalhesNorma($nuNormaExercicio, $preencheDescricao = true)
{    
    //Verifica Norma
    $arCodNorma = explode("/",$nuNormaExercicio);
    $stNumNorma = ltrim($arCodNorma[0], "0");
    if ($stNumNorma=="") {
        $stNumNorma = "0";
    }
    
    $stExercicio = $arCodNorma[1];
    if(!isset($arCodNorma[1]))
        $stExercicio = Sessao::getExercicio();
    
    $nuNormaExercicio = $arCodNorma[0]."/".$stExercicio;
    
    $stFiltroNorma = " WHERE num_norma='".$stNumNorma."' and exercicio='".$stExercicio ."'";
    $obTNorma = new TNorma();
    $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);

    if ($rsNorma->getNumLinhas() > 0) {
        $stCodNorma = $nuNormaExercicio;
        $stDataPublicacao = $rsNorma->getCampo('dt_publicacao');
        if ($preencheDescricao) {
            $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$rsNorma->getCampo('cod_tipo_norma');
            $obTTipoNorma = new TTipoNorma();
            $obTTipoNorma->recuperaTodos($rsTipoNorma, $stFiltroTipoNorma);
            $stNorma = $rsTipoNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma')."/".$rsNorma->getCampo('exercicio')." - ".$rsNorma->getCampo('nom_norma');
        }
    } else {
        $stDataPublicacao = "&nbsp;";
        $stNorma = "&nbsp;";
        $nuExercicioNorma = "";
    }

    $stJs  = "d.getElementById('stNorma').innerHTML = '".addslashes($stNorma)."';\n";
    $stJs .= "f.stNorma.value = '".addslashes($stNorma)."';\n";
    $stJs .= "f.stCodNorma.value = '".$stCodNorma."';\n";
    
    if(!isset($stCodNorma))
        $stJs .= "alertaAviso('@Campo Norma inválido(".$nuNormaExercicio.").','form','erro','".Sessao::getId()."');\n";

    echo $stJs;
}

switch ($stCtrl) {
    case "incluirAgenteLista":
        $boIncluir = true;
        unset($inCounter);

        $cmbTipoRemuneracao     = $_REQUEST['cmbTipoRemuneracao'];
        $cmbTipoNorma           = $_REQUEST['cmbTipoNorma'];
        $stCodNorma             = $_REQUEST['stCodNorma'];
        $stNorma                = $_REQUEST['stNorma'];

        $arCargosSelecionados   = $_REQUEST['arCargosSelecionados'];

        if ($cmbTipoRemuneracao != '' && $cmbTipoNorma != '' && $arCargosSelecionados != '' && $stCodNorma !='') {
            $arAgentesSessao = Sessao::read('arAgentes');

            if (is_array($arAgentesSessao)) {
                foreach ($arAgentesSessao as $arAgentesTmp) {
                    if ($arAgentesTmp['cod_tipo_remuneracao'] == $cmbTipoRemuneracao && $arAgentesTmp['cod_tipo_norma'] == $cmbTipoNorma) {
                        echo "alertaAviso('@Este Tipo de Remuneração e Tipo de Norma já estão cadastrados na Lista de Agentes Eletivos.','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
                foreach ($arAgentesSessao as $arAgentesTmp) {
                    foreach ($arAgentesTmp['cargos'] as $arAgentesCargosTmp) {
                        for($i=0;$i<count($arCargosSelecionados);$i++){
                            if ($arAgentesCargosTmp == $arCargosSelecionados[$i]) {
                                echo "alertaAviso('@O Cargo(".$arCargosSelecionados[$i].") já está cadastrado na Lista de Agentes Eletivos.','form','erro','".Sessao::getId()."');";
                                exit;
                            }
                        }
                    }
                }
            }
            
            //Verifica Norma
            $arCodNorma = explode("/",$stCodNorma);
            $arCodNorma[0] = ltrim($arCodNorma[0], "0");

            $stFiltroNorma = " WHERE num_norma='".$arCodNorma[0]."' and exercicio='".$arCodNorma[1] ."'";
            $obTNorma = new TNorma();
            $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);

            if (count($arAgentesSessao)<1) {
                $inCounter = 0;
                $inOcorrencia = 0;

                $arAgentesSessao[$inCounter]['id']                  = $inCounter;
                $arAgentesSessao[$inCounter]['cod_tipo']            = $inCounter;
                $arAgentesSessao[$inCounter]['cod_tipo_remuneracao']= $cmbTipoRemuneracao;
                $arAgentesSessao[$inCounter]['cod_tipo_norma']      = $cmbTipoNorma;
                $arAgentesSessao[$inCounter]['cod_norma']           = $rsNorma->getCampo("cod_norma");
                $arAgentesSessao[$inCounter]['norma']               = $stCodNorma;
                $arAgentesSessao[$inCounter]['stNorma']             = $stNorma;
                $arAgentesSessao[$inCounter]['cargos']              = $arCargosSelecionados;

                Sessao::write('arAgentes', $arAgentesSessao);
            } else {
                $inCounter = count($arAgentesSessao);

                $arAgentesSessao[$inCounter]['id']                  = $inCounter;
                $arAgentesSessao[$inCounter]['cod_tipo']            = $inCounter;
                $arAgentesSessao[$inCounter]['cod_tipo_remuneracao']= $cmbTipoRemuneracao;
                $arAgentesSessao[$inCounter]['cod_tipo_norma']      = $cmbTipoNorma;
                $arAgentesSessao[$inCounter]['cod_norma']           = $rsNorma->getCampo("cod_norma");
                $arAgentesSessao[$inCounter]['norma']               = $stCodNorma;
                $arAgentesSessao[$inCounter]['stNorma']             = $stNorma;
                $arAgentesSessao[$inCounter]['cargos']              = $arCargosSelecionados;

                Sessao::write('arAgentes',$arAgentesSessao);
            }

            $stJs  =  montaListaCargos("incluir");
            $stJs .= "JavaScript:passaItem('document.frm.arCargosSelecionados','document.frm.arCargosDisponiveis','tudo');\n";
            $stJs .= "jq('select#cmbTipoRemuneracao').selectOptions('');\n";
            $stJs .= "jq('select#cmbTipoNorma').selectOptions('');\n";
            $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
            $stJs .= "f.stNorma.value = '';\n";
            $stJs .= "f.stCodNorma.value = '';\n";
            echo "alertaAviso('Agente Eletivo inserido na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Selecione Tipo de Remuneração, Tipo de Norma, Norma e pelo menos um Cargo.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirAgenteLista":
        $inCount = 0;
        foreach (Sessao::read('arAgentes') as $arAgentesTmp ) {
            if ($arAgentesTmp["cod_tipo"] != $_REQUEST["inVlTipo"]) {
                $arTmp[$inCount] = $arAgentesTmp;
                $inCount++;
            }
        }

        echo "alertaAviso('Agente Eletivo excluido da lista.','','info','".Sessao::getId()."');";

        Sessao::write('arAgentes',$arTmp);
        $stJs = montaListaCargos("mostrar");
        
        echo $stJs;
    break;

    case "excluirCargo":
        $arAgentesSessao = Sessao::read('arAgentes');

        foreach ($arAgentesSessao AS $arAgentesTmp) {
            if ($arAgentesTmp['cod_tipo'] == $_REQUEST['cod_tipo']) {
                if (count($arAgentesTmp['cargos']) == 1) {
                    echo "alertaAviso('@Não é possível deletar este cargo, pois ele é o único relacionado a este agente eletivo.','form','erro','".Sessao::getId()."');";
                    die;
                }

                foreach ($arAgentesTmp['cargos'] AS $key => $value) {
                    if ($value != $_REQUEST['cod_cargo']) {
                        $arCargoNovo[] = $value;
                    }
                }
                $arAgentesTmp['cargos'] = $arCargoNovo;
            }
            $arAgentesNovasSessao[] = $arAgentesTmp;
        }

        echo "alertaAviso('Cargo deletado.','','info','".Sessao::getId()."');";

        Sessao::write('arAgentes',$arAgentesNovasSessao);
        $stJs = montaListaCargos("mostrar");
        
        echo $stJs;
    break;

    case "limparAgentesLista":
            $stJs  = "JavaScript:passaItem('document.frm.arCargosSelecionados','document.frm.arCargosDisponiveis','tudo');";
            $stJs .= "jq('select#cmbTipoRemuneracao').selectOptions('');";
            $stJs .= "jq('select#cmbTipoNorma').selectOptions('');";
            $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
            $stJs .= "f.stNorma.value = '';\n";
            $stJs .= "f.stCodNorma.value = '';\n";
            
        echo  $stJs;
    break;

    case "agentesExistentes":
        //ajustar
        $rsOcorrencia = new RecordSet;
        $obTTCEPERelacionarAgenteEletivo = new TTCEPERelacionarAgenteEletivo();
        $obTTCEPERelacionarAgenteEletivo->setDado('cod_entidade', Sessao::read('cod_entidade')  );
        $obTTCEPERelacionarAgenteEletivo->setDado('exercicio'   , Sessao::getExercicio()        );
        $obTTCEPERelacionarAgenteEletivo->listarAgenteEletivo($rsAgentes);

        $inCounter = 0;
        $arAgentesSessao = array();

        foreach ($rsAgentes->arElementos as $agente) {
            unset($arCargos);
            $arCargos = array();
            
            $stFiltroNorma = " WHERE N.cod_norma=".$agente['cod_norma'];
            $obTNorma = new TNorma();
            $obTNorma->recuperaNormasDecreto($rsNorma, $stFiltroNorma);

            $arAgentesSessao[$inCounter]['id']                  = $inCounter;
            $arAgentesSessao[$inCounter]['cod_tipo']            = $inCounter;
            $arAgentesSessao[$inCounter]['cod_tipo_remuneracao']= $agente['cod_tipo_remuneracao'];
            $arAgentesSessao[$inCounter]['cod_tipo_norma']      = $agente['cod_tipo_norma'];
            $arAgentesSessao[$inCounter]['cod_norma']           = $agente['cod_norma'];
            $arAgentesSessao[$inCounter]['norma']               = ltrim($rsNorma->getCampo("num_norma"), "0")."/".$rsNorma->getCampo("exercicio");
            $arAgentesSessao[$inCounter]['stNorma']             = $rsNorma->getCampo("nom_tipo_norma")." ".ltrim($rsNorma->getCampo("num_norma_exercicio"), "0")." - ".$rsNorma->getCampo("nom_norma");
            
            //montar Cargos
            $obTTCEPERelacionarAgenteEletivo->setDado('cod_tipo_remuneracao', $agente['cod_tipo_remuneracao']   );
            $obTTCEPERelacionarAgenteEletivo->setDado('cod_tipo_norma'      , $agente['cod_tipo_norma']         );
            $obTTCEPERelacionarAgenteEletivo->setDado('cod_norma'           , $agente['cod_norma']              );
            $obTTCEPERelacionarAgenteEletivo->listarCargoAgenteEletivo($rsCargos);
            
            foreach ($rsCargos->arElementos as $cargo) {
                $arCargos[] = $cargo['cod_cargo'];
            }
            
            $arAgentesSessao[$inCounter]['cargos']              = $arCargos;

            $inCounter++;
        }

        Sessao::write('arAgentes', $arAgentesSessao);

        $stJs = montaListaCargos("mostrar");
        echo $stJs;
    break;

    case "detalharLista":
        $obTPessoalCargo = new TPessoalCargo();
        $inCodTipo = $_REQUEST['cod_tipo'];
        $inCount = 0;

        $arAgentesSessao = Sessao::read('arAgentes');
        for ($i = 0 ;$i < count($arAgentesSessao); $i++) {
            if ($inCodTipo == $arAgentesSessao[$i]["cod_tipo"]) {
                $arListaCargo = array();
                foreach($arAgentesSessao[$i]['cargos'] as $key => $value){
                    unset($rsListaCargo);
                    $stFiltro = "WHERE PC.cod_cargo=".$value;
                    $obTPessoalCargo->listarCargos($rsListaCargo, $stFiltro," ORDER BY descricao");
    
                    while (!$rsListaCargo->eof()) {
                        $arListaCargo[$inCount]['descricao'] = $arAgentesSessao[$i]['cargos'][$key]." - ".$rsListaCargo->getCampo("descricao");
                        $arListaCargo[$inCount]['cod_cargo'] = $value;
                        $inCount++;
                        $rsListaCargo->proximo();
                    }
                }
                
                $rsCargos = new RecordSet ;
                $rsCargos->preenche($arListaCargo);
                break;
            }
        }

        while (!$rsCargos->eof()) {
            $rsCargos->setCampo('cod_tipo', $inCodTipo);
            $rsCargos->proximo();
        }

        $obTable = new Table;
        $obTable->setRecordset($rsCargos);
        $obTable->addLineNumber(false);
        $obTable->Head->addCabecalho('Cargos', 50);
        $obTable->Body->addCampo('descricao', 'E');

        $stTableAction = 'excluir';
        $stFunctionJs  = "ajaxJavaScript(&quot;OCManterAgentesEletivos.php?cod_cargo=%s&cod_tipo=%s";
        $stFunctionJs .= "&quot;,&quot;excluirCargo&quot;)";

        $obTable->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_cargo', 'cod_tipo' ) );

        $obTable->montaHTML(true);
        $stHTML = $obTable->getHtml();

        echo  $stHTML;
    break;

    case "preencherDetalhesNorma":
        preencherDetalhesNorma($_REQUEST['nuExercicioNorma'], true);
    break;
}
