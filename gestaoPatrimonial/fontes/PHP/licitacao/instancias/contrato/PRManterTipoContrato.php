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
 * Arquivo de instância para manutenção de tipo contrato
 * Data de Criação: 13/11/2015
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Jean da Silva
 *
 * $Id: PRManterTipoContrato.php 64134 2015-12-07 17:07:16Z michel $
 * 
**/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once'../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC.'TLicitacaoTipoContrato.class.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';

$stAcao = $request->get('stAcao');

$stPrograma = "ManterTipoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
$obTransacao = new Transacao();
$obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
$obErro = new Erro;

$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'), $boTransacao);

switch ($stAcao) {
    case "incluir":
        $stLink = $pgForm;

        $stFiltro = " WHERE cod_tipo = ".$request->get("inCodigo");
        $obErro = $obTLicitacaoTipoContrato->recuperaTodos($rsTipoContrato, $stFiltro, " ORDER BY tipo_tc", $boTransacao);

        if (!$obErro->ocorreu()) {
            if ($rsTipoContrato->getNumLinhas() <= 0) {
                $obTLicitacaoTipoContrato->setDado("cod_tipo" , $request->get("inCodigo")        );
                $obTLicitacaoTipoContrato->setDado("sigla"    , $request->get("stSigla")         );
                $obTLicitacaoTipoContrato->setDado("descricao", $request->get("stDescricao")     );
                $obTLicitacaoTipoContrato->setDado("tipo_tc"  , $request->get("inCodigoTribunal"));
                $obTLicitacaoTipoContrato->setDado("ativo"    , $request->get("boAtivo")         );

                if ($request->get("inCodigoTribunal") != "") {
                    $stFiltro = " WHERE tipo_tc = ".$request->get("inCodigoTribunal")."";
                    $obErro = $obTLicitacaoTipoContrato->recuperaTodos($rsResultado, $stFiltro, " ORDER BY tipo_tc", $boTransacao);
                } else {
                    $rsResultado = new RecordSet;
                }

                if (!$obErro->ocorreu()) {
                    if ($rsResultado->getNumLinhas() > 0) {
                        $obErro->setDescricao("Este código de tribunal já existe, escolha outro código!");
                    } else {
                        $obErro = $obTLicitacaoTipoContrato->inclusao($boTransacao);
                    }
                }
            }else{
                $obErro->setDescricao("O Código de Tipo de Contrato ".$request->get("inCodigo").", já está cadastrado!");
            }
        }
    break;

    case "alterar":
        $stLink = $pgFilt;

        $obTLicitacaoTipoContrato->setDado("cod_tipo" , $request->get("inCodigo")   );
        $obTLicitacaoTipoContrato->setDado("sigla"    , $request->get("stSigla")    );
        $obTLicitacaoTipoContrato->setDado("descricao", $request->get("stDescricao"));
        $obTLicitacaoTipoContrato->setDado("ativo"    , $request->get("boAtivo")    );
        $obErro = $obTLicitacaoTipoContrato->recuperaPorChave($rsResultado, $boTransacao); 

        if (!$obErro->ocorreu()) {
            if ($rsResultado->getNumLinhas() > 0) {
                if ($request->get("inCodigoTribunal") != "") {
                    $stFiltro = " WHERE tipo_tc = ".$request->get("inCodigoTribunal")."";
                    $obErro = $obTLicitacaoTipoContrato->recuperaTodos($rsResultado, $stFiltro, " ORDER BY tipo_tc", $boTransacao);
                } else {
                    $rsResultado = new RecordSet;
                }

                if (!$obErro->ocorreu()) {
                    if ($rsResultado->getNumLinhas() > 0) {
                        $obErro->setDescricao("Este código de tribunal já existe, escolha outro código!");
                    } else {
                        $obTLicitacaoTipoContrato->setDado("ativo"  , $request->get("boAtivo")         );
                        $obTLicitacaoTipoContrato->setDado("tipo_tc", $request->get("inCodigoTribunal"));
                        $obErro = $obTLicitacaoTipoContrato->alteracao($boTransacao);
                    }
                }
            }
        }

    break;

    case "excluir":
        $stLink = $pgFilt;
        $obTLicitacaoTipoContrato->setDado("cod_tipo",$request->get("inCodigo"));
        $obErro = $obTLicitacaoTipoContrato->recuperaPorChave($rsResultado, $boTransacao);

        if (!$obErro->ocorreu()) {
            $obErro = $obTLicitacaoTipoContrato->exclusao($boTransacao);
        }
    break;
}

$obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTLicitacaoTipoContrato);

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($stLink."?".Sessao::getId()."&stAcao=".$stAcao."", "Ação ".$nomAcao." concluída com sucesso! (Tipo de Contrato: ".$request->get("inCodigo").")","","aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
}