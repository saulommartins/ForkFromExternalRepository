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
    * Página de Processamento do Configuração Rais
    * Data de Criação: 25/10/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.12

    $Id: PRManterConfiguracaoRais.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoRais.class.php"                           );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAEventoComposicaoRemuneracao.class.php"                           );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAEventoHorasExtras.class.php"                           );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoRais";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$obTIMAConfiguracaoRais = new TIMAConfiguracaoRais();
$obTIMAEventoComposicaoRemuneracao = new TIMAEventoComposicaoRemuneracao();
$obTIMAEventoHorasExtras = new TIMAEventoHorasExtras();
$obTIMAEventoComposicaoRemuneracao->obTIMAConfiguracaoRais = &$obTIMAConfiguracaoRais;
$obTIMAEventoHorasExtras->obTIMAConfiguracaoRais = &$obTIMAConfiguracaoRais;

Sessao::setTrataExcecao(true);
switch ($stAcao) {
    case "incluir":
        $obTIMAConfiguracaoRais->setDado('exercicio'        ,$_POST["inExercicio"]);
        $obTIMAConfiguracaoRais->recuperaPorChave($rsConfiguracao);

        if ($rsConfiguracao->getNumLinhas() == -1) {
            $obTIMAConfiguracaoRais->setDado('numcgm'           		,$_POST["inCGM"]);
            $obTIMAConfiguracaoRais->setDado('tipo_inscricao'   		,$_POST["inTipoInscricao"]);
            $obTIMAConfiguracaoRais->setDado('telefone'         		,str_replace("-","",$_POST["stTelefone"]));
            $obTIMAConfiguracaoRais->setDado('email'            		,$_POST["stMail"]);
            $obTIMAConfiguracaoRais->setDado('natureza_juridica'		,$_POST["stNatureza"]);
            $obTIMAConfiguracaoRais->setDado('cod_municipio'    		,$_POST["inCodMunicipio"]);
            $obTIMAConfiguracaoRais->setDado('dt_base_categoria'		,$_POST["inDataBase"]);
            $obTIMAConfiguracaoRais->setDado('cei_vinculado'    	 	,$_POST["boCNPJ"]);
            $obTIMAConfiguracaoRais->setDado('cod_tipo_controle_ponto'	,$_POST["inCodTipoControlePonto"]);
            if ($_POST["boCNPJ"]) {
                $obTIMAConfiguracaoRais->setDado('numero_cei'       	,$_POST["inCei"]);
                $obTIMAConfiguracaoRais->setDado('prefixo'          	,$_POST["inPrefixo"]);
            }
            $obTIMAConfiguracaoRais->inclusao();

            foreach ($_POST["inCodEventoSelecionados"] as $inCodEvento) {
                $obTIMAEventoComposicaoRemuneracao->setDado("cod_evento",$inCodEvento);
                $obTIMAEventoComposicaoRemuneracao->inclusao();
            }
            foreach ($_POST["inCodEventoSelecionados2"] as $inCodEvento) {
                $obTIMAEventoHorasExtras->setDado("cod_evento",$inCodEvento);
                $obTIMAEventoHorasExtras->inclusao();
            }
            $stMensagem = "A configuração da RAIS para o exercício de ".$_POST["inExercicio"]." foi realizada com sucesso!";
        } else {
            $stMensagem = "A configuração da RAIS para o exercício de ".$_POST["inExercicio"]." já foi realizada!";
            Sessao::getExcecao()->setDescricao($stMensagem);
        }
        $pgRetorno = $pgForm;
        break;
    case "alterar";
        include_once CAM_GA_CGM_MAPEAMENTO.'TCGMPessoaFisica.class.php';
        $obTCGMPessoaFisica = new TCGMPessoaFisica();
        $obTCGMPessoaFisica->setDado('numcgm',$_POST["inCGM"]);
        $obTCGMPessoaFisica->recuperaPorChave($rsCGMResponsavel);
        if ($rsCGMResponsavel->eof()) {
            Sessao::getExcecao()->setDescricao('CGM do Responsável inválido! Número do CGM ('.$_POST['inCGM'].') não encontrado no cadastro de Pessoa física!');
        }

        $obTIMAConfiguracaoRais->setDado('exercicio'        		,$_POST["inExercicio"]);
        $obTIMAConfiguracaoRais->setDado('numcgm'           		,$_POST["inCGM"]);
        $obTIMAConfiguracaoRais->setDado('tipo_inscricao'   		,$_POST["inTipoInscricao"]);
        $obTIMAConfiguracaoRais->setDado('telefone'         		,str_replace("-","",$_POST["stTelefone"]));
        $obTIMAConfiguracaoRais->setDado('email'            		,$_POST["stMail"]);
        $obTIMAConfiguracaoRais->setDado('natureza_juridica'		,$_POST["stNatureza"]);
        $obTIMAConfiguracaoRais->setDado('cod_municipio'    		,$_POST["inCodMunicipio"]);
        $obTIMAConfiguracaoRais->setDado('dt_base_categoria'		,$_POST["inDataBase"]);
        $obTIMAConfiguracaoRais->setDado('cei_vinculado'    		,$_POST["boCNPJ"]);
        $obTIMAConfiguracaoRais->setDado('cod_tipo_controle_ponto'	,$_POST["inCodTipoControlePonto"]);
        $obTIMAConfiguracaoRais->setDado('numero_cei'       		,$_POST["inCei"]);
        $obTIMAConfiguracaoRais->setDado('prefixo'          		,$_POST["inPrefixo"]);
        $obTIMAConfiguracaoRais->alteracao();

        $obTIMAEventoComposicaoRemuneracao->exclusao();
        foreach ($_POST["inCodEventoSelecionados"] as $inCodEvento) {
            $obTIMAEventoComposicaoRemuneracao->setDado("cod_evento",$inCodEvento);
            $obTIMAEventoComposicaoRemuneracao->inclusao();
        }
        $obTIMAEventoHorasExtras->exclusao();
        foreach ($_POST["inCodEventoSelecionados2"] as $inCodEvento) {
            $obTIMAEventoHorasExtras->setDado("cod_evento",$inCodEvento);
            $obTIMAEventoHorasExtras->inclusao();
        }

        $pgRetorno = $pgFilt;
        $stMensagem = "A alteração da configuração da RAIS para o exercício de ".$_POST["inExercicio"]." foi realizada com sucesso!";
        break;
    case "excluir":
        $obTIMAConfiguracaoRais->setDado('exercicio'        ,$_GET["inExercicio"]);
        $obTIMAEventoComposicaoRemuneracao->exclusao();
        $obTIMAEventoHorasExtras->exclusao();
        $obTIMAConfiguracaoRais->exclusao();
        $pgRetorno = $pgFilt;
        $stMensagem = "A exclusão da configuração da RAIS para o exercício de ".$_GET["inExercicio"]." foi realizada com sucesso!";
        break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
