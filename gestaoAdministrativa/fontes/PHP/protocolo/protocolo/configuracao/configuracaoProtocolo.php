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
* Arquivo de implementação de manutenção de configuração
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24718 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:50:47 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.91
*/

include_once '../../../framework/include/cabecalho.inc.php';
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
setAjuda('uc-01.06.91');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

switch ($ctrl) {
    case 0:
        $txtCartaTemp	 = pegaConfiguracao("carta_arquivamento_temporario",5);
        $txtCartaDef	 = pegaConfiguracao("carta_arquivamento_definitivo",5);
        $caminhoRecibo	 = pegaConfiguracao("caminho_recibo_processo",      5);
        $txtRecibo       = pegaConfiguracao("mensagem_recibo_processo",     5);
        $tipoNumeracao	 = pegaConfiguracao("tipo_numeracao_processo",      5);
        $boNumeracaoClassificaoAssunto = pegaConfiguracao("tipo_numeracao_classificacao_assunto", 5);
        $numeroCopias	 = pegaConfiguracao("copias_recibo_processo",       5);
        $mascaraProcesso = pegaConfiguracao("mascara_processo",             5);
        $mascaraAssunto  = pegaConfiguracao("mascara_assunto",              5);
        $centroCusto     = pegaConfiguracao("centro_custo",                 5);
        $centroCustoNao  = ($centroCusto=='true') ? '' : 'CHECKED';
        $centroCustoSim  = ($centroCusto=='true') ? 'CHECKED' : '';

        ?>
        <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;

                campo = trim( document.frm.caminhoRecibo.value );
                campo = campo.length;
                if (campo == 0) {
                    mensagem += "@O campo Nome é obrigatório";
                    erro = true;
                }

                var expReg = /\n/g;
                campo = document.frm.textoMsg.value.replace( expReg, "");
                campo = trim(campo);
                if (campo == "") {
                    mensagem += "@O campo Mensagem é obrigatório";
                    erro = true;
                }

                campo = trim( document.frm.numeroCopias.value );
                if (campo == "") {
                    mensagem += "@O campo Nº de cópias é obrigatório";
                    erro = true;
                }

                campo = document.frm.textoCartaTemp.value.replace( expReg, "");
                campo = trim(campo);
                if (campo == "") {
                    mensagem += "@O campo Carta temporária é obrigatório";
                    erro = true;
                }

                campo = document.frm.textoCartaDef.value.replace( expReg, "");
                campo = trim(campo);
                if (campo == "") {
                    mensagem += "@O campo Carta definitiva é obrigatório";
                    erro = true;
                }

                campo = document.frm.tipoNumeracao.value;
                if (campo == 'xxx') {
                    mensagem += "@O campo Geração do código é obrigatório";
                    erro = true;
                }

                campo = trim( document.frm.mascaraProcesso.value );
                if (campo == "") {
                    mensagem += "@O campo Máscara do código do processo é obrigatório";
                    erro = true;
                }

                campo = trim( document.frm.mascaraAssunto.value );
                if (campo == "") {
                    mensagem += "@O campo Máscara do código da classificação/assunto é obrigatório";
                    erro = true;
                }

                    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                    return !(erro);
            }

            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }
        </script>
        <form action="configuracaoProtocolo.php?<?=Sessao::getId();?>" method="POST" name= "frm">
            <table width="100%">
                <tr>
                    <td class="alt_dados" colspan="2" width="30%">
                        Dados para recibos de protocolo
                    </td>
                        <input type="hidden" name="ctrl" value="1">
                </tr>

                <tr>
                    <td class="label" title="Nome do arquivo XML que define o layout do recibo de protocolo">
                        *Nome
                    </td>
                    <td class=field>
                        <input type="text" name="caminhoRecibo" size=40 value="<?=$caminhoRecibo;?>">
                        <input type="hidden" name="caminhoReciboHdn" value="<?=$caminhoRecibo;?>">
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Texto padrão para o recibo de protocolo">
                        *Mensagem
                    </td>
                    <td class="field">
                        <textarea name="textoMsg" rows=3><?=$txtRecibo;?></textarea>
                        <input type="hidden" name="textoMsgHdn" value="<?=$txtRecibo;?>">
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Número de cópias padrão para impressão do recibo de protocolo">
                        *Nº de cópias
                    </td>
                    <td class=field>
                        <input type="text" name="numeroCopias" size=4 maxlength=4 value="<?=$numeroCopias;?>">
                        <input type="hidden" name="numeroCopiasHdn" value="<?=$numeroCopias;?>">
                    </td>
                </tr>

                <tr>
                    <td class="alt_dados" colspan="2">
                        Dados para cartas de arquivamento
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Texto padrão para carta de arquivamento temporária de processos">
                        *Carta temporária
                    </td>
                    <td class="field">
                        <textarea name="textoCartaTemp" rows=3><?=$txtCartaTemp;?></textarea>
                        <input type="hidden" name="textoCartaTempHdn" value="<?=$txtCartaTemp;?>">
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Texto padrão para carta de arquivamento definitivo de processos">
                        *Carta definitiva
                    </td>
                    <td class="field">
                        <textarea name="textoCartaDef" rows=3><?=$txtCartaDef;?></textarea>
                        <input type="hidden" name="textoCartaDefHdn" value="<?=$txtCartaDef;?>">
                    </td>
                </tr>

                <tr>
                    <td class=alt_dados colspan="2">
                        Dados para processo
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Forma de geração do código de processo">
                        *Geração do código
                    </td>
                    <td class=field>
                        <select name="tipoNumeracao">
                            <?php
                                if ($tipoNumeracao == "")
                                    echo "<option value=xxx SELECTED>Selecione</option>";
                                else
                                    echo "<option value=xxx>Selecione</option>";
                                if ($tipoNumeracao == 1)
                                    echo "<option value=1 SELECTED>Automática</option>";
                                else
                                    echo "<option value=1>Automática</option>";
                                if ($tipoNumeracao == 2)
                                    echo "<option value=2 SELECTED>Manual</option>";
                                else
                                    echo "<option value=2>Manual</option>";
                            ?>
                        </select>
                        <input type="hidden" name="tipoNumeracaoHdn" value="<?=$tipoNumeracao?>">
                    </td>
                </tr>

                <tr>
                    <td class=label title="Máscara para formatação de código de processo">
                        *Máscara do código
                    </td>
                    <td class=field>
                        <input type="text" name="mascaraProcesso" value="<?=$mascaraProcesso?>" maxlength="15" size="15">
                        <input type="hidden" name="mascaraProcessoHdn" value="<?=$mascaraProcesso?>">
                    </td>
                </tr>

                <tr>
                    <td class=alt_dados colspan="2">
                        Dados para Classificação/Assunto
                    </td>
                </tr>

                <tr>
                    <td class="label" title="Forma de geração do código de processo">
                        *Geração do código
                    </td>
                    <td class=field>
                        <select name="tipoNumeracaoClassificacaoAssunto">
                            <?php
                                if ($boNumeracaoClassificaoAssunto == "")
                                    echo "<option value=xxx SELECTED>Selecione</option>";
                                else
                                    echo "<option value=xxx>Selecione</option>";
                                if ($boNumeracaoClassificaoAssunto == 'automatico')
                                    echo "<option value='automatico' SELECTED>Automático</option>";
                                else
                                    echo "<option value='automatico'>Automático</option>";
                                if ($boNumeracaoClassificaoAssunto == 'manual')
                                    echo "<option value='manual' SELECTED>Manual</option>";
                                else
                                    echo "<option value='manual'>Manual</option>";
                            ?>
                        </select>
                        <input type="hidden" name="tipoNumeracaoClassificacaoAssuntoHdn" value="<?=$boNumeracaoClassificaoAssunto?>">
                    </td>
                </tr>
                <tr>
                    <td class=label title="Máscara para formatação de código de classificação e assunto">
                        *Máscara do código
                    </td>
                    <td class=field>
                        <input type="text" name="mascaraAssunto" value="<?=$mascaraAssunto?>" maxlength="15" size="15">
                        <input type="hidden" name="mascaraAssuntoHdn" value="<?=$mascaraAssunto?>">
                    </td>
                </tr>
                
                <tr>
                    <td class=alt_dados colspan="2">
                        Dados para Centro de Custo
                    </td>
                </tr>
                <tr>
                    <td class=label title="Centro de Custo Obrigatório">
                        *Centro de Custo Obrigatório
                    </td>
                    <td class=field>
                        <input type="radio" name="centroCusto" value="true" <?=$centroCustoSim?> >Sim
                        <input type="radio" name="centroCusto" value="false" <?=$centroCustoNao?> >Não
                        <input type="hidden" name="centroCustoHdn" value="<?=$centroCusto?>">
                    </td>
                </tr>

                <tr>
                    <td class=field colspan=2 title="Botão OK salva as informações digitadas, botão limpar cancela as alterações">
                        <?php geraBotaoOk(1,0,0,0); ?>
                    </td>
                </tr>
            </table>
        </form>
<script>
<!--
document.frm.caminhoRecibo.focus();
//-->
</script>
        <?php
        break;
    case 1:
        $sql = "";
        $audit = "";
        if ($_REQUEST["tipoNumeracaoHdn"] != $_REQUEST["tipoNumeracao"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["tipoNumeracao"]."'
                        WHERE
                            parametro  = 'tipo_numeracao_processo'       AND
                            cod_modulo = 5;";
            $audit .= "Tipo de numeração de processo<br>\n";
        }

        if ($_REQUEST["tipoNumeracaoClassificacaoAssuntoHdn"] != $_REQUEST["tipoNumeracaoClassificacaoAssunto"]) {
            $sql   .=   "UPDATE administracao.configuracao
                            SET valor = '".$_REQUEST["tipoNumeracaoClassificacaoAssunto"]."'
                          WHERE parametro  = 'tipo_numeracao_classificacao_assunto'
                            AND cod_modulo = 5;";
            $audit .= "Tipo de numeração de processo<br>\n";
        }

        if ($_REQUEST["numeroCopiasHdn"] != $_REQUEST["numeroCopias"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["numeroCopias"]."'
                        WHERE
                            parametro  = 'copias_recibo_processo'        AND
                            cod_modulo = 5;";
            $audit .= "Cópias de recibo de processo<br>\n";
        }
        if ($_REQUEST["caminhoReciboHdn"] != $_REQUEST["caminhoRecibo"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["caminhoRecibo"]."'
                        WHERE
                            parametro  = 'caminho_recibo_processo'       AND
                            cod_modulo = 5;";
            $audit .= "Caminho do recibo de processo<br>\n";
        }
        if ($_REQUEST["textoCartaTempHdn"] != $_REQUEST["textoCartaTemp"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["textoCartaTemp"]."'
                        WHERE
                            parametro  = 'carta_arquivamento_temporario' AND
                            cod_modulo = 5;";
            $audit .= "Carta de arquivamento temporário<br>\n";
        }
        if ($_REQUEST["textoCartaDefHdn"] != $_REQUEST["textoCartaDef"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["textoCartaDef"]."'
                        WHERE
                            parametro  = 'carta_arquivamento_definitivo' AND
                            cod_modulo = 5;";
            $audit .= "Carta de arquivamento definitivo<br>\n";
        }
        if ($_REQUEST["textoMsgHdn"] != $_REQUEST["textoMsg"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["textoMsg"]."'
                        WHERE
                            parametro  = 'mensagem_recibo_processo'      AND
                            cod_modulo = 5;";
            $audit .= "Mensagem de recibo de processo<br>\n";
        }
        if ($mascaraProcessoHdn != $mascaraProcesso) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$mascaraProcesso."'
                        WHERE
                            parametro  = 'mascara_processo'              AND
                            cod_modulo = 5;";
            $audit .= "Máscara do código de processo<br>\n";
        }
        if ($_REQUEST["mascaraAssuntoHdn"] != $_REQUEST["mascaraAssunto"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["mascaraAssunto"]."'
                        WHERE
                            parametro  = 'mascara_assunto'               AND
                            cod_modulo = 5;";
            $audit .= "Máscara do código de Classificação/Assunto<br>\n";
        }
        if ($_REQUEST["centroCustoHdn"] != $_REQUEST["centroCusto"]) {
            $sql   .= 	"UPDATE
                            administracao.configuracao
                        SET
                            valor      = '".$_REQUEST["centroCusto"]."'
                        WHERE
                            parametro  = 'centro_custo'               AND
                            cod_modulo = 5;";
            $audit .= "Centro de Custo Obrigatório<br>\n";
        }

        if ( !empty($sql) ) {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            if ($dbConfig->executaSql($sql)) {
                include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"  );
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $audit );
                $audicao->insereAuditoria();
                echo 	'<script type="text/javascript">
                            alertaAviso("Parâmetros do protocolo","incluir","aviso","'.Sessao::getId().'");
                            window.location = "configuracaoProtocolo.php?'.Sessao::getId().'&ctrl=0";
                        </script>';
            } else {
                echo 	'<script type="text/javascript">
                            alertaAviso("Parâmetros do protocolo","erro","aviso","'.Sessao::getId().'");
                            window.location = "configuracaoProtocolo.php?'.Sessao::getId().'&ctrl=0";
                        </script>';
            }
            $dbConfig->fechaBd();
        } else {
            echo 	'<script type="text/javascript">
                    alertaAviso("Parâmetros do protocolo","incluir","aviso","'.Sessao::getId().'");
                    window.location = "configuracaoProtocolo.php?'.Sessao::getId().'&ctrl=0";
                    </script>';
        }

        break;
}
include '../../../framework/include/rodape.inc.php';
?>
