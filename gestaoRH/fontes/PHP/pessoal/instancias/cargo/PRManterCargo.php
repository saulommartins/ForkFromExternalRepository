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
    * Página de Formulario de Inclusao/Alteracao/Exclusao de Pessoal-Cargo
    * Data de Criação   : 08/12/2004

    * @author Gustavo Passos Tourinho
        * @author Vandre MIguel Ramos

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-25 16:41:29 -0300 (Qua, 25 Jul 2007) $

    * Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"  );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"    );

Sessao::remove('Alterar_Especialidade');

$stAcao = $request->get('stAcao');
$arLink = Sessao::read('link');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargo";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obRPessoalCargo = new RPessoalCargo;
$obRPessoalCargo->listarVagas ( $rsRegimento );

$inNumLinhas = $rsRegimento->getNumLinhas ();
$obErro = new Erro;
switch ($stAcao) {
    case "incluir":
        $boCargo = $boFuncao = $boTemVagasPreenchidas = false;
        $arVagas = $arSubDivisao = array ();

        $inVagas = 0;
        foreach ($_POST as $Campo => $value) {
            if (substr($Campo,0,6)=='stVaga') {
                $codSubDivisao = explode('_', $Campo);
                $inValor = (trim($_POST[$Campo]) != "") ? $_POST[$Campo] : 0;
                $arVagas[] = $codSubDivisao[2]."_".$inValor;
                $inVagas += $inValor;
            }
        }

        if ($inVagas == 0) {
            $arEspecialidades = Sessao::read('arEspecialidades');
            if (!empty($arEspecialidades)) {
                foreach ($arEspecialidades as $campo => $valor) {
                        $inVagas += $valor['inVagas'];
                }
            }
        }

        if ($inVagas < 1) {
            sistemaLegado::exibeAviso("A soma total de vagas do cargo deve ser maior que zero (0)."," "," ");
        } else {
            if ( $_REQUEST["rdCargo"] == "S" )
                $boCargo = true;
            if ( $_REQUEST["rdFuncao"] == "S" )
                $boFuncao = true;

            // Dados comuns aos cargos com ou sem especialidade
            $obRPessoalCargo->setDescricao         ( trim($_REQUEST["stDescricao"])           );
            $obRPessoalCargo->setCargo             ( $boCargo                                 );
            $obRPessoalCargo->setFuncao            ( $boFuncao                                );
            $obRPessoalCargo->setCBO               ( $_REQUEST["inCodCBO"]                    );
            $obRPessoalCargo->setCodEscolaridade   ( $_REQUEST["inCodEscolaridadeMinima"]     );
            $obRPessoalCargo->setAtribuicoes       ( $_REQUEST["stAtribuicoes"]               );
            $obRPessoalCargo->setCodRequisitos     ( $_REQUEST["inCodRequisitosSelecionados"] );

            if ($_REQUEST["boEspecialidade"] == "S" || $_REQUEST['hdnboEspecialidade']) {
                // Dados relativos aos cargos COM especialidade
                if (!empty($arEspecialidades)) {
                    foreach ( Sessao::read('arEspecialidades') as $Especialidade ) {
                        $rsNorma    = new RecordSet();
                        $obTNorma   = new TNorma();
                        $arCodNorma = explode("/",$Especialidade['stCodNorma']);

                        if (sizeof($arCodNorma)>0) {
                            $nuNorma   = ltrim($arCodNorma[0],"0");
                            $nuNorma   = ($nuNorma!="")?$nuNorma:"0";
                            $stFiltro  = " AND num_norma = '".$nuNorma."'";
                            $stFiltro .= " AND exercicio = '".$arCodNorma[1]."'";
                            $stFiltro  = " WHERE ".substr($stFiltro,4);
                            $obTNorma->recuperaTodos($rsNorma,$stFiltro);
                        }
                        $obRPessoalCargo->addEspecialidade ();
                        $obRPessoalCargo->roUltimoEspecialidade->setDescricaoEspecialidade             ( trim($Especialidade["stDescricaoEspecialidade"]) );
                        $obRPessoalCargo->roUltimoEspecialidade->setCBOEspecialidade                   ( $Especialidade["inCodCBO"]       );
                        $obRPessoalCargo->roUltimoEspecialidade->obRFolhaPagamentoPadrao->setCodPadrao ( $Especialidade["inCodPadrao"] );
                        $obRPessoalCargo->roUltimoEspecialidade->obRNorma->setCodNorma ( $rsNorma->getCampo("cod_norma") );
                        $obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                        $obRPessoalCargo->roUltimoEspecialidade->roUltimoEspecialidadeSubDivisao->setNroVagas       ( $Especialidade["arVagas"] );
                        $obRPessoalCargo->roUltimoEspecialidade->roUltimoEspecialidadeSubDivisao->setNroVagasCriada ( $Especialidade["arVagas"] );

                    }
                }
            } else {
                // Dados relativos aos cargos SEM especialidade
                $obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao ( $_REQUEST["inCodPadrao"] );
                $obRPessoalCargo->setCBO                                ( $_REQUEST["inCodCBO"]    );

              $rsNorma    = new RecordSet();
              $obTNorma   = new TNorma();
              $arCodNorma = explode("/",$_REQUEST['stCodNorma']);
              if (sizeof($arCodNorma)>0) {
                  $nuNorma   = ltrim($arCodNorma[0],"0");
                  $nuNorma   = ($nuNorma!="")?$nuNorma:"0";
                  $stFiltro  = " AND num_norma = '".$nuNorma."'";
                  $stFiltro .= " AND exercicio = '".$arCodNorma[1]."'";
                    $stFiltro  = " WHERE ".substr($stFiltro,4);
                    $obTNorma->recuperaTodos($rsNorma,$stFiltro);
              }

                $obRPessoalCargo->obRNorma->setCodNorma ( $rsNorma->getCampo('cod_norma') );
                $obRPessoalCargo->addCargoSubDivisao ();
                $obRPessoalCargo->roUltimoCargoSubDivisao->setNroVagas       ( $arVagas    );
                $obRPessoalCargo->roUltimoCargoSubDivisao->setNroVagasCriada ( $arVagas    );
            }

            //monta array de atributos dinamicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode( "," , $value );
                }
                $obRPessoalCargo->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            if (!Sessao::read('arEspecialidades') && ($_REQUEST["boEspecialidade"] == "S" || $_REQUEST['hdnboEspecialidade'])) {
                sistemaLegado::exibeAviso("É necessário incluir uma especialidade."," "," ");
            } else {
                $obErro = $obRPessoalCargo->incluirCargo ();

                if ( !$obErro->ocorreu() ) {

                    // caso a opção de vincular o novo cargo aos eventos já cadastrados for selecionada no Alert, irá realizar a operação abaixo
                    if ($_REQUEST['boVincularEventos'] == "true") {
                        $obErroEvento = $obRPessoalCargo->incluirVincularCargoEventos();
                        if ($obErroEvento->ocorreu()) {
                            sistemaLegado::exibeAviso(urlencode($obErroEvento->getDescricao()),"n_incluir","erro");
                        }
                    }

                    Sessao::remove('arEspecialidades');
                    sistemaLegado::alertaAviso($pgForm.$stLink,"Cargo : ".$obRPessoalCargo->getCodCargo()." - ".$_REQUEST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
            }
        }
        break;
    case "alterar":
        $boCargo = $boFuncao = $boTemVagasPreenchidas = false;
        $arVagas = $arSubDivisao = array ();

        $obErro = new Erro();
        $inCodCargo = $_REQUEST["hdnCodCargo"];

        $inCodEspecialidade = Sessao::read('inCodEspecialidade');
        $arEspecialidade = Sessao::read("arEspecialidades");
        $inId = Sessao::read("inId");
        //Monta um array com o código da subdivisao e com a quantidade de vagas disponiveis (codigo_vagas_disponiveis)
        //Monta outro array com o código da subdivisao e com a quantidade de vagas criadas (codigo_vagas_criadas)
        if (empty($inCodEspecialidade)) {
            foreach ($_POST as $Campo => $value) {
                if (substr($Campo,0,6)=='stVaga') {
                    //Divide o codigo do numero de vagas (codigo_vagas)
                    //$arCodigos = explode('_',$Campo);
                    $arEspecialidadeSubDivisaoVaga[$Campo] = $value;
                    $arCodigos = preg_replace('/^([A-Za-z]+_)/', '', $Campo);
                    $inCodRegime = preg_replace('/(^[\d]+)_(.*)/', '$1', $arCodigos);
                    $inCodSubDivisao = preg_replace('/([\d]+_)([\d]+)(.*)/', '$2', $arCodigos);

                    $obRPessoalCargo->setCBO        ( $_REQUEST["inCodCBO"]      );

                    //Busca os valores atuais (valores que estão no banco ainda sem a alteração)
                    $obRPessoalCargo->addCargoSubDivisao();
                    $obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );
                    $obRPessoalCargo->setCodCargo( $inCodCargo );
                    $obRPessoalCargo->roUltimoCargoSubDivisao->listarVagas( $rsVagas, $stFiltro);

                    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php");
                    $obTPessoalCargoSubDivisao = new TPessoalCargoSubDivisao();
                    $obTPessoalCargoSubDivisao->setDado("cod_cargo",$inCodCargo);
                    $obTPessoalCargoSubDivisao->setDado("cod_regime",$inCodRegime);
                    $obTPessoalCargoSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                    $obTPessoalCargoSubDivisao->getVagasOcupadasCargo($rsOcupadas);
                    $inVagasOcupadas = $rsOcupadas->getCampo("vagas");

                    if ( $value < $inVagasOcupadas && trim($value) != "" ) {
                        $obErro->setDescricao("O número de novas vagas da sub-divisão ".$rsVagas->getCampo('nom_sub_divisao')." tem que ser maior ou igual ao número de vagas ocupadas(".$inVagasOcupadas.").");
                        break;
                    }

                    $inVagasCriadas = (trim($value) != "") ? $value : 0;
                    $arVagasCriadas[]   = $inCodSubDivisao."_".$inVagasCriadas;

                    if ($value) {
                        $boTemVagasPreenchidas = true;
                    }
                }
            }
        } else {
            foreach ($arEspecialidade as $especialidade) {
                $valor = 0;
                $inCodRegime = 0;
                $inCodSubDivisao = 0;
                foreach ($especialidade["arVagas"] as $campo => $value) {

                    $valor = $value;
                    $arCodigos = preg_replace('/^([A-Za-z]+_)/', '', $campo);
                    $inCodRegime = preg_replace('/(^[\d]+)_(.*)/', '$1', $arCodigos);
                    $inCodSubDivisao = preg_replace('/([\d]+_)([\d]+)(.*)/', '$2', $arCodigos);

                    $obRPessoalCargo->setCBO        ( $especialidade["inCodCBO"] );
                    $obRPessoalCargo->addCargoSubDivisao();
                    $obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );
                    $obRPessoalCargo->setCodCargo( $inCodCargo );
                    $obRPessoalCargo->roUltimoCargoSubDivisao->listarVagas( $rsVagas, $stFiltro);

                    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php");
                    $obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao();
                    $obTPessoalEspecialidadeSubDivisao->setDado("cod_especialidade",$especialidade["inCodEspecialidade"]);
                    $obTPessoalEspecialidadeSubDivisao->setDado("cod_regime",$inCodRegime);
                    $obTPessoalEspecialidadeSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                    $obTPessoalEspecialidadeSubDivisao->getVagasOcupadasEspecialidade($rsOcupadas);
                    $inVagasOcupadas = $rsOcupadas->getCampo("vagas") ;

                    if ( $valor < $inVagasOcupadas && trim($valor) != "" ) {
                        $obErro->setDescricao("O número de novas vagas da sub-divisão ".$rsVagas->getCampo('nom_sub_divisao')." tem que ser maior ou igual ao número de vagas ocupadas(".$inVagasOcupadas.").");
                        break;
                    }

                    $inVagasCriadas = (trim($valor) != "") ? $valor : 0;
                    $arVagasCriadas[]   = $inCodSubDivisao."_".$inVagasCriadas;

                    if ($valor > 0) {
                        $boTemVagasPreenchidas = true;
                    }
                }
            }
        }

        Sessao::remove("inCodEspecialidade");

/*
        if (Sessao::read('arEspecialidades')) {
            $arEspecialidades = Sessao::read('arEspecialidades');
            $arEspecialidades[$inArrayEspecialidadesId]['arVagas'] = $arEspecialidadeSubDivisaoVaga;
            Sessao::write('arEspecialidades', $arEspecialidades);
        }
*/
//        if (!$obErro->ocorreu()) {
//            if ($_REQUEST["boEspecialidade"] == "N" && !$boTemVagasPreenchidas) {
//                $obErro->setDescricao("A soma total de vagas do cargo deve ser maior que zero (0).");
//            }
//        }

        if (!$obErro->ocorreu()) {
            if ($_POST["rdCargo"] == "S") {
                $boCargo = true;
            } else {
                $boCargo = false;
            }
            if ($_POST["rdFuncao"] == "S") {
                $boFuncao = true;
            } else {
                $boFuncao = false;
            }
            // Dados comuns aos cargos com ou sem especialidade
            $obRPessoalCargo->setCodCargo        ( $inCodCargo                          );
            $obRPessoalCargo->setDescricao       ( $_REQUEST["stDescricao"]             );
            $obRPessoalCargo->setCargo           ( $boCargo                             );
            $obRPessoalCargo->setFuncao          ( $boFuncao                            );
            $obRPessoalCargo->setCBO             ( $_REQUEST["inCodCBO"]                );
            $obRPessoalCargo->setCodEscolaridade ( $_REQUEST["inCodEscolaridadeMinima"] );
            $obRPessoalCargo->setAtribuicoes     ( $_REQUEST["stAtribuicoes"]           );
            $obRPessoalCargo->setCodRequisitos   ( $_REQUEST["inCodRequisitosSelecionados"] );

            //monta array de atributos dinamicos
            foreach ($arChave as $key => $value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode( "," , $value );
                }
                $obRPessoalCargo->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }

            if ($_REQUEST["boEspecialidade"] == "S") {
                if (!Sessao::read('arEspecialidades')) {
                    $obErro->setDescricao("É necessário incluir uma especialidade.");
                }

                if (!$obErro->ocorreu()) {
                    //Exclui as especialidades
                    $obCargoEspecialidadeExcluir = new RPessoalCargo;
                    $obCargoEspecialidadeExcluir->addEspecialidade();
                    $arExcluirEspecialidade = Sessao::read('arEspecialidadeExcluir');
                    if (!empty($arExcluirEspecialidade)) {
                        foreach ($arExcluirEspecialidade as $campo => $inCodEspecialidade) {
                            $obCargoEspecialidadeExcluir->roUltimoEspecialidade->setCodEspecialidade($inCodEspecialidade);
                            $obErro = $obCargoEspecialidadeExcluir->roUltimoEspecialidade->excluirEspecialidade();
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    // Dados relativos aos cargos COM especialidade
                    $obRPessoalCargo->setEspecialidade(true);

                    foreach ( Sessao::read('arEspecialidades') as $Especialidade ) {
                        $rsNorma    = new RecordSet();
                        $obTNorma   = new TNorma();
                        $arCodNorma = explode("/",$Especialidade['stCodNorma']);
                        if (sizeof($arCodNorma)>0) {
                            $nuNorma   = ltrim($arCodNorma[0],"0");
                            $nuNorma   = ($nuNorma!="")?$nuNorma:"0";
                            $stFiltro  = " AND num_norma = '".$nuNorma."'";
                            $stFiltro .= " AND exercicio = '".$arCodNorma[1]."'";
                            $stFiltro  = " WHERE ".substr($stFiltro,4);
                            $obTNorma->recuperaTodos($rsNorma,$stFiltro);
                        }

                        $obRPessoalCargo->addEspecialidade ();
                        $obRPessoalCargo->roUltimoEspecialidade->setDescricaoEspecialidade             ( $Especialidade["stDescricaoEspecialidade"] );
                        $obRPessoalCargo->roUltimoEspecialidade->setCBOEspecialidade                   ( $Especialidade["inCodCBO"]       );
                        $obRPessoalCargo->roUltimoEspecialidade->obRFolhaPagamentoPadrao->setCodPadrao ( $Especialidade["inCodPadrao"] );
                        $obRPessoalCargo->roUltimoEspecialidade->obRNorma->setCodNorma                 ( $rsNorma->getCampo("cod_norma")  );
                        $obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                        $obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $Especialidade["inCodEspecialidade"] );
                        $obRPessoalCargo->roUltimoEspecialidade->roUltimoEspecialidadeSubDivisao->setNroVagas        ( $Especialidade["arVagas"] );
                        $obRPessoalCargo->roUltimoEspecialidade->roUltimoEspecialidadeSubDivisao->setNroVagasCriada  ( $Especialidade["arVagas"] );
                    }
                }
            } else {
                // Cargo SEM especialidade
                // Dados relativos aos cargos SEM especialidade
                $obRPessoalCargo->setEspecialidade(false);
                $obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao ( $_REQUEST["inCodPadrao"] );
                $obRPessoalCargo->setCBO                                ( $_REQUEST["inCodCBO"]    );

              $rsNorma    = new RecordSet();
              $obTNorma   = new TNorma();
              $arCodNorma = explode("/",$_REQUEST['stCodNorma']);
              if (sizeof($arCodNorma)>0) {
                  $nuNorma   = ltrim($arCodNorma[0],"0");
                  $nuNorma   = ($nuNorma!="")?$nuNorma:"0";
                  $stFiltro  = " AND num_norma = '".$nuNorma."'";
                  $stFiltro .= " AND exercicio = '".$arCodNorma[1]."'";
                    $stFiltro  = " WHERE ".substr($stFiltro,4);
                    $obTNorma->recuperaTodos($rsNorma,$stFiltro);
                }

                $obRPessoalCargo->obRNorma->setCodNorma ( $rsNorma->getCampo('cod_norma') );
                $obRPessoalCargo->addCargoSubDivisao ();
                $obRPessoalCargo->roUltimoCargoSubDivisao->setNroVagas       ( $arVagasDisponiveis );
                $obRPessoalCargo->roUltimoCargoSubDivisao->setNroVagasCriada ( $arVagasCriadas     );
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRPessoalCargo->alterarCargo();
            if ( !$obErro->ocorreu() ) {
                Sessao::remove('arEspecialidades');
                sistemaLegado::alertaAviso($pgList,"Cargo : ".$_REQUEST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;
    case "excluir":
        $obRPessoalCargo->setCodCargo      ( $_REQUEST["inCodCargo"] );
        $obErro = $obRPessoalCargo->excluirCargo();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Cargo: ".$_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
        break;
}
?>
