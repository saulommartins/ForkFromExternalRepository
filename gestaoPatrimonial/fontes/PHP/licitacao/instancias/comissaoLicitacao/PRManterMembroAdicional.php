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
    * Pagina de formulário para Cadastro de Membro adicional
    * Data de Criação   : 10/04/2014

    * @author Desenvolvedor: Lisiane Morais

    * @ignore

    * $Id: PRManterMembroAdicional.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoMembroAdicional.class.php"                             );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterMembroAdicional";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$arMembrosAdicionais = Sessao::read('arMembrosAdicionais');
$stAcao = $_REQUEST['stAcao'];
$boTransacao = new Transacao();

switch ($stAcao) {
    case 'alterar':
        $obErro = new Erro;
        Sessao::setTrataExcecao(true);
        if (!$obErro->ocorreu()) {
            //Altera os campos cargo e natureza_cargo dos membros
            $obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional;
            $inCount = 0;
            $inCodLicitacaoAux = 0;
            
            if (is_array($arMembrosAdicionais)) {                
                foreach ($arMembrosAdicionais as $campo => $registro) {
                    $inCount++;
                    $obTLicitacaoMembroAdicional->setDado( 'cod_licitacao'    , $registro['cod_licitacao']  );
                    $obTLicitacaoMembroAdicional->setDado( 'cod_modalidade'   , $registro['cod_modalidade'] );
                    $obTLicitacaoMembroAdicional->setDado( 'cod_entidade'     , $registro['cod_entidade']   );
                    $obTLicitacaoMembroAdicional->setDado( 'exercicio'        , $registro['exercicio']      );
                    $obTLicitacaoMembroAdicional->setDado( 'numcgm'           , $registro['numcgm']         );
                    $obTLicitacaoMembroAdicional->recuperaPorChave($rsMembrosAdicionaisComissao);

                    if ($rsMembrosAdicionaisComissao->getNumLinhas() > 0) {
                        if ($registro['cod_licitacao'] != $inCodLicitacaoAux) {
                            $inCount = 1;
                            $inCodLicitacaoAux = $registro['cod_licitacao'];
                        }
                        $obTLicitacaoMembroAdicional->setDado( 'cargo'           , trim($_REQUEST["stCargoMembro_".$registro['numcgm']."_".$registro['cod_licitacao']."_".$inCount ]) );
                        $obTLicitacaoMembroAdicional->setDado( 'natureza_cargo'  , trim($_REQUEST["inNaturezaCargo_".$registro['numcgm']."_".$registro["cod_licitacao"]."_".$inCount])  );
                        $obTLicitacaoMembroAdicional->alteracao($boTransacao);
                    } else {
                        $obTLicitacaoMembroAdicional->setDado( 'cargo'           , trim($_REQUEST['stCargoMembro_'.$registro['numcgm']."_".$registro['cod_licitacao']."_".$inCount ]) );
                        $obTLicitacaoMembroAdicional->setDado( 'natureza_cargo'  , trim($_REQUEST["inNaturezaCargo_".$registro['numcgm']."_".$registro["cod_licitacao"]."_".$inCount ]) );
                        $obTLicitacaoMembroAdicional->inclusao($boTransacao);
                    }   
                }
            }else{
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Nenhum dado foi preenchido","n_incluir","aviso", Sessao::getId(), "../");
            }
        }
        Sessao::encerraExcecao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=alterar","Comissão alterada.","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;
    }
?>
