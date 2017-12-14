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
    * Página de Processamento para Arquivar Processo em Lote.
    * Data de Criação: 23/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: PRManterProcessoEmLote.php 65751 2016-06-14 19:15:31Z jean $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_PROT_MAPEAMENTO."TProcessoArquivado.class.php"									);
include_once ( CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php"												);
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoEmLote";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obTProcessoArquivado = new TProcessoArquivado();
$obTAuditoria         = new TAuditoria;
$obTProcesso          = new TProcesso();
$obErro               = new Erro();

$stAcao = $request->get('stAcao');

//Array da Lista de Processo em Lote
$arListaProcessos = Sessao::read("arListaProcesso");

if (!$obErro->ocorreu()) {
    if ($arListaProcessos != "") {
        // Abre-se uma transação para poder atualizar dos os dados da tabela
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $obTransacao);
    
        if (!$obErro->ocorreu()) {
            foreach ($arListaProcessos as $processo) {
                $obTProcesso->setDado('cod_processo', $processo["cod_processo"]);
                $obTProcesso->setDado('ano_exercicio',$processo["ano_exercicio"]);
                $obErro = $obTProcesso->consultar($boTransacao);
        
                if (!$obErro->ocorreu()) {
                    $obTProcessoArquivado->setDado("cod_processo"           , $processo["cod_processo"]);
                    $obTProcessoArquivado->setDado("ano_exercicio"          , $processo["ano_exercicio"]);
                    $obTProcessoArquivado->recuperaPorChave($rsChaveProcesso, $boTransacao);
        
                    if ($rsChaveProcesso->getNumLinhas() > 0) {
                        $obErro->descricao("Este processo já foi arquivado!");
                    }
        
                    if (!$obErro->ocorreu()) {
                        $obTProcessoArquivado->setDado("cod_historico"          , $request->get('stHistorico'));
                        $obTProcessoArquivado->setDado("texto_complementar"     , $request->get('txtComplementar'));
                        $obTProcessoArquivado->setDado("localizacao"            , $request->get('stLocalizacaoFisica'));
                        $obTProcessoArquivado->setDado("timestamp_arquivamento" , date( "Y-m-d H:i:s.ms" ));
                        $obTProcessoArquivado->setDado("cgm_arquivador"         , Sessao::read("numCgm"));
                        $obErro = $obTProcessoArquivado->inclusao($boTransacao);
                    }
                }
        
                if (!$obErro->ocorreu()) {
                    $obTProcesso->setDado("cod_situacao", $request->get('stTipo'));
                    $obTProcesso->alteracao($boTransacao);
                }
            }
        }

        $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTProcessoArquivado);
    } else {
        $obErro->setDescricao("Devem haver processos selecionados para serem arquivados!");
    }
}

Sessao::remove("arListaProcesso");

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Processo(s) arquivado(s) com sucesso!","aviso","aviso", Sessao::getId(), "../");
} else {
    $obErro->setDescricao("Erro auditado");
    SistemaLegado::LiberaFrames(true, false);
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$obErro->getDescricao(),"n_incluir","erro", Sessao::getId(), "../");
}