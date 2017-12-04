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
    * Oculto do Componente FiltroMultiploRegSubCarEsp
    * Data de Criacão: 22/02/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                        );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidade.class.php"                                );

function preencherSubDivisao($stFunc="",$boExecuta=false)
{
    $arCodSubDivisaoSelecionados = array();

    $stJs .= "limpaSelect(f.inCodSubDivisaoDisponiveis".$stFunc.",0);  \n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados".$stFunc.",0); \n";
    $stJs .= "if (f.inCodFuncaoDisponiveis) { limpaSelect(f.inCodFuncaoDisponiveis,0);  } \n";
    $stJs .= "if (f.inCodFuncaoSelecionados) { limpaSelect(f.inCodFuncaoSelecionados,0); } \n";
    $stJs .= "if (f.inCodCargoDisponiveis) { limpaSelect(f.inCodCargoDisponiveis,0);   } \n";
    $stJs .= "if (f.inCodCargoSelecionados) { limpaSelect(f.inCodCargoSelecionados,0);  } \n";
    $stJs .= "if (f.inCodEspecialidadeDisponiveis".$stFunc.") { limpaSelect(f.inCodEspecialidadeDisponiveis".$stFunc.",0);  }  \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionados".$stFunc.") { limpaSelect(f.inCodEspecialidadeSelecionados".$stFunc.",0); }  \n";

    if ( is_array($_POST['inCodRegimeSelecionados'.$stFunc]) ) {
        foreach ($_POST['inCodRegimeSelecionados'.$stFunc] as $inCodRegime) {
            $stCodRegime .= $inCodRegime.",";
        }
        $stCodRegime = substr($stCodRegime,0,strlen($stCodRegime)-1);
        $obRPessoalSubDivisao = new RPessoalSubDivisao( new RPessoalRegime );
        $obRPessoalSubDivisao->listarSubDivisaoDeCodigosRegime($rsSubDivisao,$stCodRegime);

        $inIndexDisponiveis  = 0;
        $inIndexSelecionados = 0;

        while ( !$rsSubDivisao->eof() ) {
            $boDisponiveis = false;

            // Inserindo Funcões Disponiveis
            if (is_array($_POST["inCodSubDivisaoDisponiveis".$stFunc]) && count($_POST["inCodSubDivisaoDisponiveis".$stFunc])>0) {
                if (in_array($rsSubDivisao->getCampo('cod_sub_divisao'), $_POST["inCodSubDivisaoDisponiveis".$stFunc])) {
                    $arCodSubDivisaoDisponiveis[] = $rsSubDivisao->getCampo('cod_sub_divisao');
                    $stJs .= "f.inCodSubDivisaoDisponiveis".$stFunc."[".$inIndexDisponiveis."] = new Option('".$rsSubDivisao->getCampo('nom_sub_divisao')."','".$rsSubDivisao->getCampo('cod_sub_divisao')."','');\n";
                    $inIndexDisponiveis++;
                    $boDisponiveis = true;
                }
            }

            // Inserindo Funcões Selecionadas
            if ($boDisponiveis === false) {
                $stJs .= "f.inCodSubDivisaoSelecionados".$stFunc."[".$inIndexSelecionados."] = new Option('".$rsSubDivisao->getCampo('nom_sub_divisao')."','".$rsSubDivisao->getCampo('cod_sub_divisao')."','');\n";
                $inIndexSelecionados++;
            }
            $rsSubDivisao->proximo();
        }
    }
    unset($_POST["inCodSubDivisaoDisponiveis".$stFunc]);
    $_POST["inCodSubDivisaoDisponiveis".$stFunc] = $arCodSubDivisaoDisponiveis;

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherCargo($boExecuta=false)
{
    $arCodCargoSelecionadosCorrigidos = array();

    $stJs .= "if (f.inCodCargoDisponiveis) { limpaSelect(f.inCodCargoDisponiveis,0);  } \n";
    $stJs .= "if (f.inCodCargoSelecionados) { limpaSelect(f.inCodCargoSelecionados,0); } \n";
    $stJs .= "if (f.inCodEspecialidadeDisponiveisFunc) { limpaSelect(f.inCodEspecialidadeDisponiveisFunc,0);  }   \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionadosFunc) { limpaSelect(f.inCodEspecialidadeSelecionadosFunc,0); }   \n";
    $stJs .= "if (f.inCodEspecialidadeDisponiveis) { limpaSelect(f.inCodEspecialidadeDisponiveis,0);  }           \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionados) { limpaSelect(f.inCodEspecialidadeSelecionados,0); }           \n";

    if ( is_array($_POST['inCodSubDivisaoSelecionados']) && count($_POST['inCodSubDivisaoSelecionados'])) {
        $stCodSubDivisao = implode(",",$_POST['inCodSubDivisaoSelecionados']);

        include_once CAM_GRH_PES_NEGOCIO.'RPessoalCargo.class.php';
        $obRPessoalCargo = new RPessoalCargo();

        $obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($stCodSubDivisao);

        if (isset($_POST['inAno'])) {
            $stExercicio = $_POST['inAno'];
        } elseif ($_POST['stExercicio']) {
            $stExercicio = $_POST['stExercicio'];
        } elseif ($_POST['inAnoCompetencia']) {
            $stExercicio = $_POST['inAnoCompetencia'];
        } else {
            $stExercicio = Sessao::getExercicio();
        }

        if (isset($_POST['inCodMes'])) {
            $obRPessoalCargo->setCodMes($_POST['inCodMes']);
        }

        $obRPessoalCargo->setExercicio($stExercicio);
        $obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargos);

        $inIndexDisponiveis  = 0;
        $inIndexSelecionados = 0;

        while ( !$rsCargos->eof() ) {
            $boselecionado = false;

            // Inserindo Funcões Selecionadas
            if (is_array($_POST["inCodCargoSelecionados"]) && count($_POST["inCodCargoSelecionados"])>0) {
                if (in_array($rsCargos->getCampo('cod_cargo'), $_POST["inCodCargoSelecionados"])) {
                    $arCodCargoSelecionadosCorrigidos[] = $rsCargos->getCampo('cod_cargo');
                    $stJs .= "f.inCodCargoSelecionados[".$inIndexSelecionados."] = new Option('".$rsCargos->getCampo('descricao')."','".$rsCargos->getCampo('cod_cargo')."','');\n";
                    $inIndexSelecionados++;
                    $boselecionado = true;
                }
            }

            // Inserindo Funcões Disponiveis
            if ($boselecionado === false) {
                $stJs .= "f.inCodCargoDisponiveis[".$inIndexDisponiveis."] = new Option('".$rsCargos->getCampo('descricao')."','".$rsCargos->getCampo('cod_cargo')."','');\n";
                $inIndexDisponiveis++;
            }
            $rsCargos->proximo();
        }
    }
    unset($_POST['inCodCargoSelecionados']);
    $_POST['inCodCargoSelecionados'] = $arCodCargoSelecionadosCorrigidos;

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherFuncao($boExecuta=false)
{
    $arCodFuncaoSelecionadosCorrigidos = array();

    $stJs .= "if (f.inCodFuncaoDisponiveis) { limpaSelect(f.inCodFuncaoDisponiveis,0);  }                         \n";
    $stJs .= "if (f.inCodFuncaoSelecionados) { limpaSelect(f.inCodFuncaoSelecionados,0); }                         \n";
    $stJs .= "if (f.inCodEspecialidadeDisponiveisFunc) { limpaSelect(f.inCodEspecialidadeDisponiveisFunc,0);  }   \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionadosFunc) { limpaSelect(f.inCodEspecialidadeSelecionadosFunc,0); }   \n";
    $stJs .= "if (f.inCodEspecialidadeDisponiveis) { limpaSelect(f.inCodEspecialidadeDisponiveis,0);  }           \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionados) { limpaSelect(f.inCodEspecialidadeSelecionados,0); }           \n";

    if ( is_array($_POST['inCodSubDivisaoSelecionadosFunc']) && count($_POST['inCodSubDivisaoSelecionadosFunc'])>0) {
        $stCodSubDivisao = implode(",",$_POST['inCodSubDivisaoSelecionadosFunc']);

        include_once CAM_GRH_PES_NEGOCIO.'RPessoalCargo.class.php';
        $obRPessoalCargo = new RPessoalCargo();

        $obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($stCodSubDivisao);

        if (isset($_POST['inAno'])) {
            $stExercicio = $_POST['inAno'];
        } elseif ($_POST['stExercicio']) {
            $stExercicio = $_POST['stExercicio'];
        } elseif ($_POST['inAnoCompetencia']) {
            $stExercicio = $_POST['inAnoCompetencia'];
        } else {
            $stExercicio = Sessao::getExercicio();
        }

        if (isset($_POST['inCodMes'])) {
            $obRPessoalCargo->setCodMes($_POST['inCodMes']);
        }

        $obRPessoalCargo->setExercicio($stExercicio);
        $obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargos);

        $inIndexDisponiveis  = 0;
        $inIndexSelecionados = 0;

        while (!$rsCargos->eof()) {
            $boselecionado = false;

            // Inserindo Funcões Selecionadas
            if (is_array($_POST["inCodFuncaoSelecionados"]) && count($_POST["inCodFuncaoSelecionados"])>0) {
                if (in_array($rsCargos->getCampo('cod_cargo'), $_POST["inCodFuncaoSelecionados"])) {
                    $arCodFuncaoSelecionadosCorrigidos[] = $rsCargos->getCampo('cod_cargo');
                    $stJs .= "f.inCodFuncaoSelecionados[".$inIndexSelecionados."] = new Option('".$rsCargos->getCampo('descricao')."','".$rsCargos->getCampo('cod_cargo')."','');\n";
                    $inIndexSelecionados++;
                    $boselecionado = true;
                }
            }

            // Inserindo Funcões Disponiveis
            if ($boselecionado === false) {
                $stJs .= "f.inCodFuncaoDisponiveis[".$inIndexDisponiveis."] = new Option('".$rsCargos->getCampo('descricao')."','".$rsCargos->getCampo('cod_cargo')."','');\n";
                $inIndexDisponiveis++;
            }
            $rsCargos->proximo();
        }
    }
    unset($_POST['inCodFuncaoSelecionados']);
    $_POST['inCodFuncaoSelecionados'] = $arCodFuncaoSelecionadosCorrigidos;

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherEspecialidade($stFunc="",$boExecuta=false)
{
    $stJs .= "if (f.inCodEspecialidadeDisponiveis".$stFunc.") { limpaSelect(f.inCodEspecialidadeDisponiveis".$stFunc.",0);  } \n";
    $stJs .= "if (f.inCodEspecialidadeSelecionados".$stFunc.") { limpaSelect(f.inCodEspecialidadeSelecionados".$stFunc.",0); } \n";

    if ($stFunc != "") {
        $arCodigos = $_POST['inCodFuncaoSelecionados'];
    } else {
        $arCodigos = $_POST['inCodCargoSelecionados'];
    }

    if ( is_array($arCodigos) && count($arCodigos)>0) {
        foreach ($arCodigos as $inCodCargo) {
            $stCodCargo .= $inCodCargo.",";
        }
        $stCodCargo = substr($stCodCargo,0,strlen($stCodCargo)-1);
        $obRPessoalEspecialidade = new RPessoalEspecialidade( new RPessoalCargo );
        $obRPessoalEspecialidade->listarEspecialidadeDeCodigosCargo($rsEspecialidades,$stCodCargo);

        $inIndexDisponiveis  = 0;
        $inIndexSelecionados = 0;

        while ( !$rsEspecialidades->eof() ) {
            $boselecionado = false;

            // Inserindo Funcões Selecionadas
            if (is_array($_POST["inCodEspecialidadeSelecionados".$stFunc]) && count($_POST["inCodEspecialidadeSelecionados".$stFunc])>0) {
                if (in_array($rsEspecialidades->getCampo('cod_especialidade'), $_POST["inCodEspecialidadeSelecionados".$stFunc])) {
                    $stJs .= "f.inCodEspecialidadeSelecionados".$stFunc."[".$inIndexSelecionados."] = new Option('".$rsEspecialidades->getCampo('descricao')."','".$rsEspecialidades->getCampo('cod_especialidade')."','');\n";
                    $inIndexSelecionados++;
                    $boselecionado = true;
                }
            }

            // Inserindo Especialidade Disponiveis
            if ($boselecionado === false && count($arCodigos)>0) {
                $stJs .= "f.inCodEspecialidadeDisponiveis".$stFunc."[".$inIndexDisponiveis."] = new Option('".$rsEspecialidades->getCampo('descricao')."','".$rsEspecialidades->getCampo('cod_especialidade')."','');\n";
                $inIndexDisponiveis++;
            }
            $rsEspecialidades->proximo();
        }
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

switch ($_REQUEST["stTipoBusca"]) {
    case "preencherSubDivisao":
        $stJs .= preencherSubDivisao();
        $stJs .= preencherCargo();
        $stJs .= preencherEspecialidade();
    break;
    case "preencherSubDivisaoFunc":
        $stJs .= preencherSubDivisao("Func");
        $stJs .= preencherFuncao();
        $stJs .= preencherEspecialidade("Func");
    break;
    case "preencherCargo":
        $stJs .= preencherCargo();
        $stJs .= preencherEspecialidade();
    break;
    case "preencherCargoFunc":
        $stJs .= preencherFuncao();
        $stJs .= preencherEspecialidade("Func");
    break;
    case "preencherEspecialidade":
        $stJs .= preencherEspecialidade();
    break;
    case "preencherEspecialidadeFunc":
        $stJs .= preencherEspecialidade("Func");
    break;
}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}
?>
