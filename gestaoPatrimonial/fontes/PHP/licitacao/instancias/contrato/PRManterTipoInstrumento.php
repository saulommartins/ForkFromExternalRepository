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
 * Arquivo de instância para manutenção de tipo instrumento
 * Data de Criação: 26042016
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Lisiane da Rosa Morais
 *
 * $Id: PRManterTipoInstrumento.php 65229 2016-05-04 13:48:40Z michel $
 * 
**/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once'../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC.'TLicitacaoTipoInstrumento.class.php';
include_once CAM_FW_BANCO_DADOS.'Transacao.class.php';

$stAcao = $request->get('stAcao');

$stPrograma = "ManterTipoInstrumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));

$obTLicitacaoTipoInstrumento = new TLicitacaoTipoInstrumento();

$obErro = new Erro;
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

if (!$obErro->ocorreu()) {
    switch ($stAcao) {
        case "incluir":
            $stLink = $pgForm;

            $stFiltro = " WHERE cod_tipo = ".$request->get("inCodigo");
            $obErro = $obTLicitacaoTipoInstrumento->recuperaTodos($rsTipoInstrumento, $stFiltro, " ORDER BY codigo_tc", $boTransacao);

            if (!$obErro->ocorreu()) {
                if ($rsTipoInstrumento->getNumLinhas() <= 0) {
                    $obTLicitacaoTipoInstrumento->setDado("cod_tipo" , $request->get("inCodigo")        );
                    $obTLicitacaoTipoInstrumento->setDado("descricao", $request->get("stDescricao")     );
                    $obTLicitacaoTipoInstrumento->setDado("codigo_tc", $request->get("inCodigoTribunal"));
                    $obTLicitacaoTipoInstrumento->setDado("ativo"    , $request->get("boAtivo")         );

                    if ($request->get("inCodigoTribunal") != "") {
                        $stFiltro = " WHERE codigo_tc = ".$request->get("inCodigoTribunal")."";
                        $obErro = $obTLicitacaoTipoInstrumento->recuperaTodos($rsResultado, $stFiltro, " ORDER BY codigo_tc", $boTransacao);
                    } else
                        $rsResultado = new RecordSet;

                    if (!$obErro->ocorreu()) {
                        if ($rsResultado->getNumLinhas() > 0)
                            $obErro->setDescricao("Este código de tribunal já existe, escolha outro código!");
                        else
                            $obErro = $obTLicitacaoTipoInstrumento->inclusao($boTransacao);
                    }
                }else
                    $obErro->setDescricao("O Código de Tipo de Instrumento ".$request->get("inCodigo").", já está cadastrado!");
            }
        break;

        case "alterar":
            $stLink = $pgFilt;

            $obTLicitacaoTipoInstrumento->setDado("cod_tipo" , $request->get("inCodigo")   );
            $obTLicitacaoTipoInstrumento->setDado("descricao", $request->get("stDescricao"));
            $obTLicitacaoTipoInstrumento->setDado("ativo"    , $request->get("boAtivo")    );
            $obErro = $obTLicitacaoTipoInstrumento->recuperaPorChave($rsResultado, $boTransacao); 

            if (!$obErro->ocorreu()) {
                if ($rsResultado->getNumLinhas() > 0) {
                    if ($request->get("inCodigoTribunal") != "") {
                        $stFiltro = " WHERE codigo_tc = ".$request->get("inCodigoTribunal")." AND cod_tipo NOT IN (".$request->get("inCodigo").") ";
                        $obErro = $obTLicitacaoTipoInstrumento->recuperaTodos($rsResultado, $stFiltro, " ORDER BY codigo_tc", $boTransacao);
                    } else
                        $rsResultado = new RecordSet;

                    if (!$obErro->ocorreu()) {
                        if ($rsResultado->getNumLinhas() > 0)
                            $obErro->setDescricao("Este código de tribunal já existe, escolha outro código!");
                        else {
                            $obTLicitacaoTipoInstrumento->setDado("ativo"    , $request->get("boAtivo")         );
                            $obTLicitacaoTipoInstrumento->setDado("codigo_tc", $request->get("inCodigoTribunal"));
                            $obErro = $obTLicitacaoTipoInstrumento->alteracao($boTransacao);
                        }
                    }
                }
            }

        break;

        case "excluir":
            $stLink = $pgFilt;
            $obTLicitacaoTipoInstrumento->setDado("cod_tipo", $request->get("inCodigo"));
            $obErro = $obTLicitacaoTipoInstrumento->recuperaPorChave($rsResultado, $boTransacao);

            if (!$obErro->ocorreu())
                $obErro = $obTLicitacaoTipoInstrumento->exclusao($boTransacao);
        break;
    }
}

$obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTLicitacaoTipoInstrumento);

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($stLink."?".Sessao::getId()."&stAcao=".$stAcao."", "Ação ".$nomAcao." concluída com sucesso! (Tipo de Instrumento: ".$request->get("inCodigo").")","","aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
}