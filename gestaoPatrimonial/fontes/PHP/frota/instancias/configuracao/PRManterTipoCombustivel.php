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

    * $Id: PRManterTipoCombustivel.php 63209 2015-08-04 18:18:08Z jean $

    * Casos de uso: uc-03.02.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaCombustivel.class.php" );

$stPrograma = "ManterTipoCombustivel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaCombustivel = new TFrotaCombustivel();

    switch ($stAcao) {
        case 'incluir':

            $obErro = new Erro;
            $boFlagTransacao = false;
            $obTransacao = new Transacao;
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            if (!$obErro->ocorreu()) {
                //verifica se nao existe ja no cadastro o nome da marca
                $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, " WHERE nom_combustivel ILIKE '".$request->get('stCombustivel')."' ","",$boTransacao );

                if ( $rsCombustivel->getNumLinhas() > 0 ) {
                    $obErro->setDescricao('Já existe um combustível com esta descrição');
                }
    
                if (!$obErro->ocorreu()) {

                    if ($request->get('inCodCombustivel') != '') {
                        $inCodCombustivel = $request->get('inCodCombustivel');
                        //verifica se já foi informado o codigo do veiculo
                        $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, " WHERE cod_combustivel = ".$inCodCombustivel." ","",$boTransacao );

                        if ($rsCombustivel->getNumLinhas() > 0) {
                            $obErro->setDescricao('Já existe um combustível com este código');
                        }
                    } else {
                        //recupera o cod_combustivel
                        $obTFrotaCombustivel->ProximoCod( $inCodCombustivel,$boTransacao );
                    }

                    if (!$obErro->ocorreu()) {
                        //seta os dados e cadastra no sistema
                        $obTFrotaCombustivel->setDado( 'cod_combustivel', $inCodCombustivel );
                        $obTFrotaCombustivel->setDado( 'nom_combustivel', $request->get('stCombustivel') );
                        $obErro = $obTFrotaCombustivel->inclusao($boTransacao);
                    }
                }

                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$inCodCombustivel.' - '.$request->get('stCombustivel'),"incluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_incluir","erro");
                }

                $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTFrotaCombustivel);
            }
    
        break;
    
        case 'alterar':
            $obErro = new Erro;
            $boFlagTransacao = false;
            $obTransacao = new Transacao;
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            if (!$obErro->ocorreu()) {
                //verifica se nao existe ja no cadastro o nome da marca
                $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, " WHERE nom_combustivel ILIKE '".$request->get('stCombustivel')."' AND cod_combustivel <> ".$request->get('inCodCombustivel')." " );
    
                if ( $rsCombustivel->getNumLinhas() > 0 ) {
                    $obErro->setDescricao('Já existe um combustível com esta descrição');
                }

                if (!$obErro->ocorreu()) {
                    //seta os dados e cadastra no sistema
                    $obTFrotaCombustivel->setDado( 'cod_combustivel', $request->get('inCodCombustivel') );
                    $obTFrotaCombustivel->setDado( 'nom_combustivel', $request->get('stCombustivel') );
                    $obErro = $obTFrotaCombustivel->alteracao();
                }

                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$request->get('inCodCombustivel').' - '.$request->get('stCombustivel'),"alterar","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_incluir","erro");
                }
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTFrotaCombustivel);
    
            break;
    
        case 'excluir':
            $obErro = new Erro;
            $boFlagTransacao = false;
            $obTransacao = new Transacao;
            $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

            if (!$obErro->ocorreu()) {
                //seta os dados e exclui da base
                $obTFrotaCombustivel->setDado( 'cod_combustivel', $request->get('inCodCombustivel') );
                $obErro = $obTFrotaCombustivel->exclusao();

                if (!$obErro->ocorreu()) {
                    SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Tipo de Combustível - '.$request->get('inCodCombustivel').' - '.$request->get('stNomCombustivel'),"excluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_incluir","erro");
                }
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTFrotaCombustivel);

        break;
    }
?>