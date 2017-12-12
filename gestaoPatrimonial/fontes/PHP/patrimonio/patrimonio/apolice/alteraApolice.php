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
    * Arquivo que altera a situação do processo, ele retorna mensagem de sucesso ou de erro
    * Data de Criação   : 11/03/2003

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.23  2006/07/21 11:34:42  fernando
Inclusão do  Ajuda.

Revision 1.22  2006/07/13 19:51:06  fernando
Alteração de hints

Revision 1.21  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.20  2006/07/06 12:11:27  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include '../apolice.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.08");

    // operacoes no frame oculto
    switch ($controle) {
        // busca a seguradora a partir do codigo fornecido
        case 1:
            $js = "f.controle.value = 0; \n";
            if ($codSeguradora > 0) {
                if (!$nomSeguradora = pegaDado("nom_cgm","sw_cgm","Where numcgm = ".$codSeguradora."")) {
                    $nomSeguradora = "";
                    $js .= "erro = true;\n";
                    $js .= 'mensagem += "@Campo Seguradora inválido(Código: '.$codSeguradora.').";';
                    $js .= 'f.codSeguradora.value = "";';
                    $js .= 'f.codSeguradora.focus();';
               }
            } else {
                $nomSeguradora = "";
                $js .= 'f.codSeguradora.value = "";';
            }

            $js .= 'f.nomSeguradora.value = "'.$nomSeguradora.'" ;';
            executaFrameOculto($js);

            exit();
        break;

        case 2:
//            if ( !is_numeric($numero) && $numero ) {
//                $js .= "erro = true;\n";
//                $js .= 'mensagem += "@Campo Número inválido(Código: '.$numero.').";';
//                $js .= 'f.numero.value = "";';
//
//                executaFrameOculto($js);
//            }
//            exit();
        break;
    }
    // encerra operacoes no frame oculto

    // instancia objeto da classe APOLICE.CLASS
    $apolice = new apolice;
    $apolice->codigo = $codigo;

    if (!(isset($ctrl)))
        $ctrl=0;

    switch ($ctrl) {

        // formulario para exibicao/alteracao da apolice selecionada
        case 0:

        $apolice->mostraApolice($codigo);
?>
        <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = document.frm.numero.value.length;
                if (campo == 0) {
                mensagem += "@O campo Número é obrigatório.";
                erro = true;
            }

            campo = document.frm.codSeguradora.value.length;
                if (campo == 0) {
                mensagem += "@O campo Seguradora é obrigatório.";
                erro = true;
            }

            campo = parseInt(document.frm.codSeguradora.value);
            if (!(campo <= 2147483647)) {
             mensagem += "@O campo Seguradora excedido.";
             erro = true;
            }

            campo = document.frm.dtVencimento.value.length;
                if (campo == 0) {
                mensagem += "@O campo Vencimento é obrigatório.";
                erro = true;
            }

            campo = document.frm.contato.value.length;
                if (campo == 0) {
                mensagem += "@O campo Contato é obrigatório.";
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

        // funcao que busca Seguradora no frame oculto
        function validacao(cod)
        {
            var f = document.frm;
            f.target = 'oculto';
            f.controle.value = cod;
            f.submit();
        }

        function Cancela()
        {
            mudaTelaPrincipal("listaApolice.php?<?=Sessao::getId();?>&ctrl=0&pagina=<?=$pagina;?>");
        }

        </script>

        <form action="alteraApolice.php?<?=Sessao::getId();?>&ctrl=1" method="POST" name="frm">

        <input type='hidden' name='controle' value="">
        <input type="hidden" name="codigo" value="<?=$apolice->codigo;?>">
        <input type='hidden' name='pagina' value="<?=$pagina;?>">

        <table width = "100%">

        <tr><td class="alt_dados" colspan="2">Dados da Apólice</td></tr>
        <tr>
            <td class="label" title="Número da apólice." width="20%">*Número</td>
            <td class="field" width="80%">
                <input type="text" name="numero" size="10" maxlength="10" onKeyPress="return(isValido(this, event, '0123456789abcdefghijklmnopqrstuvxywzABCDEFGHIJKLMNOPQRSTUVXYWZ'))"  value="<?=$apolice->numero;?>">
            </td>
        </tr>
        <tr>
            <td class="label" title="Selecione a seguradora.">*Seguradora</td>
            <td class=field>
                <input type="text" name="codSeguradora" value="<?=$apolice->codSeguradora;?>" size="9" maxlength="9" onKeyPress="return(isValido(this, event, '0123456789'))" onChange="validacao(1);">
                <input type="text" name="nomSeguradora" size="60" readonly="" value="<?=$apolice->nomSeguradora;?>"> &nbsp;
                <a href="javascript:procurarCgm('frm','codSeguradora','nomSeguradora','juridica','<?=Sessao::getId()?>');">
                <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar seguradora" border="0" align="absmiddle">
            </td>
        </tr>
<?php
geraCampoData2("*Data de Vencimento", "dtVencimento", $apolice->dtVencimento, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data de vencimento da apólice","Buscar data de vencimento" );
?>
        <tr>
            <td class="label" title="Dados do contato (nome, telefone, etc)">*Contato</td>
            <td class="field">
                <input type="text" name="contato" size="40" maxlength="40" value="<?=$apolice->contato;?>">
            </td>
        </tr>
        <tr>
            <td class=field colspan=2>
                <?=geraBotaoAltera();?>
            </td>
        </tr>

        </table>

        </form>
<?php
        break;

        // faz update nos dados da apólice do BD com as alterações feitas no formulario
        case 1:
            $apolice->codigo        = $codigo;
            $apolice->numero        = $numero;
            $apolice->codSeguradora = $codSeguradora;
            $apolice->nomSeguradora = $nomSeguradora;
            $apolice->dtVencimento  = $dtVencimento;
            $apolice->contato       = $contato;

            if (comparaValor("num_apolice", $numero,"patrimonio.apolice", "and numcgm = $codSeguradora and cod_apolice <> $codigo")) {
                if ($apolice->alteraApolice($codigo)) {
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                    $audicao = new auditoriaLegada;;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $apolice->numero);
                    $audicao->insereAuditoria();
                    alertaAviso("listaApolice.php?pagina=$pagina","Apólice ".$numero." - ".$nomSeguradora , "alterar","aviso");

                } else {
                    alertaAviso("listaApolice.php","Apólice ".$numero." - ".$nomSeguradora,"n_alterar","erro");
                }

            } else {
                alertaAviso("alteraApolice.php","O Número: ".$numero." desta Apólice, já está cadastrada na Seguradora: ".$nomSeguradora,"unica","erro");
            }

            break;
    }

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
