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
    * Oculto da regra de interface para localização
    * Data de Criação: 11/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package administracao
    * @subpackage componentes

    $Revision: 27737 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-24 14:57:09 -0200 (Qui, 24 Jan 2008) $

    * Casos de uso: uc-01.01.00
*/

/*
$Log$
Revision 1.2  2007/10/05 13:02:21  hboaventura
inclusão dos arquivos

Revision 1.1  2007/09/18 15:09:58  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TUnidade.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TDepartamento.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TSetor.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TLocal.class.php" );
include_once( CAM_GA_ADM_COMPONENTES."IMontaLocalizacao.class.php");

$obIMontaLocalizacao = Sessao::read('obIMontaLocalizacao');
//pega a mascara do local
$arMascara = explode('.',sistemaLegado::pegaConfiguracao('mascara_local',2) );
switch ($_REQUEST['stCtrl']) {
    //preenche o combo unidade
    case 'preencheComboUnidade' :
        //limpa os combos posteriores a orgao
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectUnidade->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectUnidade->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectDepartamento->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectSetor->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '';";
        if ( $_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] != '' ) {
            //recupera todas as unidades de acordo com o codigo do orgao
            $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
            $stFiltro = " WHERE cod_orgao = ".$arOrgao[0]."
                            AND ano_exercicio = '".$arOrgao[1]."' ";
            $obTUnidade = new TUnidade();
            $obTUnidade->recuperaTodos( $rsUnidade, $stFiltro );

            //preenche o combo de unidades
            $inCount = 1;
            while ( !$rsUnidade->eof() ) {
                $stJs.= "f.".$obIMontaLocalizacao->obSelectUnidade->getName()."[".$inCount."] = new Option('".$rsUnidade->getCampo('nom_unidade')."','".str_pad($rsUnidade->getCampo('cod_unidade'),strlen($arMascara[1]),0,STR_PAD_LEFT)."','');";
                $rsUnidade->proximo();
                $inCount++;
            }
            //monta o código da localização
            $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".$arOrgao[0].".';";
        }

        break;
    case 'preencheComboDepartamento' :
        $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
        //limpa os combos posteriores a unidade
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectDepartamento->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectSetor->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".str_pad($arOrgao[0],strlen($arMascara[0]),0,STR_PAD_LEFT).".';";
        if ( $_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()] != '' ) {
            //recupera todos os departamentos de acordo com o filtro
            $stFiltro = "
                WHERE cod_orgao = ".$arOrgao[0]."
                  AND cod_unidade = ".$_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()]."
                  AND ano_exercicio = '".$arOrgao[1]."'
            ";

            $obTDepartamento = new TDepartamento();
            $obTDepartamento->recuperaTodos( $rsDepartamento, $stFiltro );

            //preenche o combo de departamentos
            $inCount = 1;
            while ( !$rsDepartamento->eof() ) {
                $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName()."[".$inCount."] = new Option('".$rsDepartamento->getCampo('nom_departamento')."','".str_pad($rsDepartamento->getCampo('cod_departamento'),strlen($arMascara[2]),0,STR_PAD_LEFT)."','');";
                $rsDepartamento->proximo();
                $inCount++;
            }
            //monta o código da localização
            $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = $('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value +'".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()],strlen($arMascara[1]),0,STR_PAD_LEFT).".';";
        }
        break;
    case 'preencheComboSetor' :
        $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
        //limpa os combos posteriores a departamento
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectSetor->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".str_pad($arOrgao[0],strlen($arMascara[0]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()],strlen($arMascara[1]),0,STR_PAD_LEFT).".';";
        if ( $_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()] != '' ) {
            //recupera todos os setores de acordo com o filtro
            $stFiltro = "
                WHERE cod_orgao = ".$arOrgao[0]."
                  AND cod_unidade = ".$_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()]."
                  AND cod_departamento = ".$_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()]."
                  AND ano_exercicio = '".$arOrgao[1]."'
            ";

            $obTSetor = new TSetor();
            $obTSetor->recuperaTodos( $rsSetor, $stFiltro );

            //preenche o combo de setores
            $inCount = 1;
            while ( !$rsSetor->eof() ) {
                $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName()."[".$inCount."] = new Option('".$rsSetor->getCampo('nom_setor')."','".str_pad($rsSetor->getCampo('cod_setor'),strlen($arMascara[3]),0,STR_PAD_LEFT)."','');";
                $rsSetor->proximo();
                $inCount++;
            }

            //monta o código da localização
            $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = $('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value +'".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()],strlen($arMascara[2]),0,STR_PAD_LEFT).".';";
        }
        break;
    case 'preencheComboLocal' :
        $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
        //limpa os combos posteriores a setor
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";

        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".str_pad($arOrgao[0],strlen($arMascara[0]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()],strlen($arMascara[1]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()],strlen($arMascara[2]),0,STR_PAD_LEFT).".';";
        if ( $_REQUEST[$obIMontaLocalizacao->obSelectSetor->getName()] != '' ) {
            //recupera todos os locais de acordo com o filtro
            $stFiltro = "
                WHERE cod_orgao = ".$arOrgao[0]."
                  AND cod_unidade = ".$_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()]."
                  AND cod_departamento = ".$_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()]."
                  AND cod_setor = ".$_REQUEST[$obIMontaLocalizacao->obSelectSetor->getName()]."
                  AND ano_exercicio = '".$arOrgao[1]."'
            ";

            $obTLocal = new TLocal();
            $obTLocal->recuperaTodos( $rsLocal, $stFiltro );

            $arMascaraLocal = explode('/',$arMascara[4]);

            //preenche o combo de locais
            $inCount = 1;
            while ( !$rsLocal->eof() ) {
                $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName()."[".$inCount."] = new Option('".$rsLocal->getCampo('nom_local')."','".str_pad($rsLocal->getCampo('cod_local'),strlen($arMascaraLocal[0]),0,STR_PAD_LEFT)."','');";
                $rsLocal->proximo();
                $inCount++;
            }

            //monta o código da localização
            $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = $('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value +'".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectSetor->getName()],strlen($arMascara[3]),0,STR_PAD_LEFT).".';";
        }
        break;
    case 'preencheComboCodLocalizacao' :
        $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
        //limpa os combos posteriores a setor

        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".str_pad($arOrgao[0],strlen($arMascara[0]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()],strlen($arMascara[1]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()],strlen($arMascara[2]),0,STR_PAD_LEFT).".".str_pad($_REQUEST[$obIMontaLocalizacao->obSelectSetor->getName()],strlen($arMascara[3]),0,STR_PAD_LEFT).".';";
        if ( $_REQUEST[$obIMontaLocalizacao->obSelectLocal->getName()] != '' ) {
            $arOrgao = explode('/',$_REQUEST[$obIMontaLocalizacao->obSelectOrgao->getName()] );
            //recupera todos os locais de acordo com o filtro
            $stFiltro = "
                WHERE cod_orgao = ".$arOrgao[0]."
                  AND cod_unidade = ".$_REQUEST[$obIMontaLocalizacao->obSelectUnidade->getName()]."
                  AND cod_departamento = ".$_REQUEST[$obIMontaLocalizacao->obSelectDepartamento->getName()]."
                  AND cod_setor = ".$_REQUEST[$obIMontaLocalizacao->obSelectSetor->getName()]."
                  AND cod_local = ".$_REQUEST[$obIMontaLocalizacao->obSelectLocal->getName()]."
                  AND ano_exercicio = '".$arOrgao[1]."'
            ";

            $obTLocal = new TLocal();
            $obTLocal->recuperaTodos( $rsLocal, $stFiltro );

            $arMascaraLocal = explode('/',$arMascara[4]);

            //monta o código da localização
            $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = $('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value +'".str_pad($rsLocal->getCampo( 'cod_local' ),strlen($arMascaraLocal[0]),0,STR_PAD_LEFT)."/".$rsLocal->getCampo( 'ano_exercicio' )."';";
        }
        break;
    case 'preencheCombos' :
        //preenche os outros combos de acordo com o código da localizacao
        $stJs.= "$('".$obIMontaLocalizacao->obSelectOrgao->getId()."').selectedIndex = 0;";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectUnidade->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectUnidade->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectDepartamento->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectSetor->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
        $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";
        $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '';";

        if ( strlen($_REQUEST[$obIMontaLocalizacao->obTxtLocalizacao->getName()]) == 23  ) {

            $arLocalizacao = explode( '.',$_REQUEST[$obIMontaLocalizacao->obTxtLocalizacao->getName()] );
            $arLocalizacao[4] = explode( '/', $arLocalizacao[4] );

            //recupera pela chave o local
            $stFiltro = "
                WHERE cod_orgao = ".$arLocalizacao[0]."
                  AND cod_unidade = ".$arLocalizacao[1]."
                  AND cod_departamento = ".$arLocalizacao[2]."
                  AND cod_setor = ".$arLocalizacao[3]."
                  AND cod_local = ".$arLocalizacao[4][0]."
                  AND ano_exercicio = '".$arLocalizacao[4][1]."'
            ";

            $obTLocal = new TLocal();
            $obTLocal->recuperaTodos( $rsLocal, $stFiltro );

            if ( $rsLocal->getNumLinhas() > 0 ) {

                //seleciona o orgao escolhido, caso nao existe, mostra um erro
                $stJs .= "
                 for (i=0;i<$('".$obIMontaLocalizacao->obSelectOrgao->getId()."').length;i++) {
                        if ($('".$obIMontaLocalizacao->obSelectOrgao->getId()."').options[i].value == '".$arLocalizacao[0]."/".$arLocalizacao[4][1]."') {
                           $('".$obIMontaLocalizacao->obSelectOrgao->getId()."').selectedIndex = i;
                        }
                    }
                ";

                //recupera todas as unidades de acordo com o codigo do orgao
                $stFiltro = " WHERE cod_orgao = ".$arLocalizacao[0]."
                                AND ano_exercicio = '".$arLocalizacao[4][1]."' ";
                $obTUnidade = new TUnidade();
                $obTUnidade->recuperaTodos( $rsUnidade, $stFiltro );

                //preenche o combo de unidades
                $inCount = 1;
                while ( !$rsUnidade->eof() ) {
                    //verifica se o registro é igual a chave e deixa ele selecionado no select
                    $stSelected = ( (int) $arLocalizacao[1] == $rsUnidade->getCampo('cod_unidade') ) ? 'selected' : '';

                    $stJs.= "f.".$obIMontaLocalizacao->obSelectUnidade->getName()."[".$inCount."] = new Option('".$rsUnidade->getCampo('nom_unidade')."','".str_pad($rsUnidade->getCampo('cod_unidade'),strlen($arLocalizacao[1]),0,STR_PAD_LEFT)."','".$stSelected."');";
                    $inCount++;
                    $rsUnidade->proximo();
                }

                //recupera todos os departamentos de acordo com o filtro
                $stFiltro .= "
                      AND cod_unidade = ".$arLocalizacao[1]."
                ";

                $obTDepartamento = new TDepartamento();
                $obTDepartamento->recuperaTodos( $rsDepartamento, $stFiltro );

                //preenche o combo de departamentos
                $inCount = 1;
                while ( !$rsDepartamento->eof() ) {
                    //verifica se o registro é igual a chave e deixa ele selecionado no select
                    $stSelected = ( (int) $arLocalizacao[2] == $rsDepartamento->getCampo('cod_departamento') ) ? 'selected' : '';

                    $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName()."[".$inCount."] = new Option('".$rsDepartamento->getCampo('nom_departamento')."','".str_pad($rsDepartamento->getCampo('cod_departamento'),strlen($arLocalizacao[2]),0,STR_PAD_LEFT)."','".$stSelected."');";
                    $inCount++;
                    $rsDepartamento->proximo();
                }

                //recupera todos os setores de acordo com o filtro
                $stFiltro .= "
                      AND cod_departamento = ".$arLocalizacao[2]."
                ";

                $obTSetor = new TSetor();
                $obTSetor->recuperaTodos( $rsSetor, $stFiltro );
                //$obTSetor->debug();

                //preenche o combo de setores
                $inCount = 1;
                while ( !$rsSetor->eof() ) {
                    //verifica se o registro é igual a chave e deixa ele selecionado no select
                    $stSelected = ( (int) $arLocalizacao[3] == $rsSetor->getCampo('cod_setor') ) ? 'selected' : '';

                    $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName()."[".$inCount."] = new Option('".$rsSetor->getCampo('nom_setor')."','".str_pad($rsSetor->getCampo('cod_setor'),strlen($arLocalizacao[3]),0,STR_PAD_LEFT)."','".$stSelected."');";
                    $inCount++;
                    $rsSetor->proximo();
                }

                //recupera todos os locais de acordo com o filtro
                $stFiltro .= "
                      AND cod_setor = ".$arLocalizacao[3]."
                ";

                $obTLocal = new TLocal();
                $obTLocal->recuperaTodos( $rsLocal, $stFiltro );

                //preenche o combo de locais
                $inCount = 1;
                while ( !$rsLocal->eof() ) {
                    //verifica se o registro é igual a chave e deixa ele selecionado no select
                    $stSelected = ( (int) $arLocalizacao[4][0] == $rsLocal->getCampo('cod_local') ) ? 'selected' : '';

                    $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName()."[".$inCount."] = new Option('".$rsLocal->getCampo('nom_local')."','".str_pad($rsLocal->getCampo('cod_local'),strlen($arLocalizacao[4][0]),0,STR_PAD_LEFT)."','".$stSelected."');";
                    $inCount++;
                    $rsLocal->proximo();
                }
                $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '".$_REQUEST[$obIMontaLocalizacao->obTxtLocalizacao->getName()]."';";
            } else {
                $stJs = "alertaAviso('@Código da Localização inválido!','form','erro','".Sessao::getId()."');";
                $stJs.= "$('".$obIMontaLocalizacao->obSelectOrgao->getId()."').selectedIndex = 0;";
                $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectUnidade->getName().",0);";
                $stJs.= "f.".$obIMontaLocalizacao->obSelectUnidade->getName().".options[0] = new Option('Selecione','','selected');";
                $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectDepartamento->getName().",0);";
                $stJs.= "f.".$obIMontaLocalizacao->obSelectDepartamento->getName().".options[0] = new Option('Selecione','','selected');";
                $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectSetor->getName().",0);";
                $stJs.= "f.".$obIMontaLocalizacao->obSelectSetor->getName().".options[0] = new Option('Selecione','','selected');";
                $stJs.= "limpaSelect(f.".$obIMontaLocalizacao->obSelectLocal->getName().",0);";
                $stJs.= "f.".$obIMontaLocalizacao->obSelectLocal->getName().".options[0] = new Option('Selecione','','selected');";
                $stJs.= "$('".$obIMontaLocalizacao->obTxtLocalizacao->getId()."').value = '';";
            }

        }
        break;

}

echo $stJs;
