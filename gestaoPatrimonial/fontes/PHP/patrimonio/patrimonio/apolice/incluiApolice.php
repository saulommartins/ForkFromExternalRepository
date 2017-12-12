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
    * Arquivo que inclui uma nova apólice, ele retorna mensagem de sucesso ou de erro
    * Data de Criação   : 24/03/2003

    * @author Analista Jorge B. Ribarr

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.26  2006/07/21 11:34:42  fernando
Inclusão do  Ajuda.

Revision 1.25  2006/07/13 19:51:06  fernando
Alteração de hints

Revision 1.24  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.23  2006/07/06 12:11:27  diego

*/
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../apolice.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
    setAjuda("UC-03.01.08");
    // operacoes no frame oculto
    switch ($controle) {
        // busca a seguradora a partir do codigo fornecido
        case 1:
            $js = "f.controle.value = 0; \n";
            if ($codSeguradora > 0) {
                if (!$nomSeguradora = pegaDado("nom_cgm","sw_cgm","Where numcgm = '".$codSeguradora."'")) {
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
            exit();
        break;
    }
    // encerra operacoes no frame oculto

    // instancia objeto da classe Apolice
    $Apolice = new Apolice;

    if (!(isset($ctrl)))
        $ctrl=0;

    switch ($ctrl) {

        // Formulario para INCLUSAO de Apolice
        case 0:
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
                    document.frm.controle.value = 0;
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

        </script>

        <form action="incluiApolice.php?<?=Sessao::getId();?>&ctrl=1" method="POST" name="frm">

        <input type='hidden' name='controle' value="">

        <table width = "100%">
        <tr><td class=alt_dados colspan=2>Dados da Apólice</td></tr>
        <tr>
            <td class="label" title="Número da apólice." width="20%">*Número</td>
            <td class="field" width="80%">
                <input type="text" name="numero" size="10" maxlength="15" value="<?=$numero;?>" onKeyPress="return(isValido(this, event, '0123456789abcdefghijklmnopqrstuvxywzABCDEFGHIJKLMNOPQRSTUVXYWZ'))" >
            </td>
        </tr>
        <tr>
            <td class="label" title="Selecione a seguradora.">*Seguradora</td>
            <td class="field">
                <input type="text" name="codSeguradora" value="<?=$codSeguradora;?>"size="9" maxlength="9" onKeyPress="return(isValido(this, event, '0123456789'))" onChange="validacao(1);">
                <input type="text" name="nomSeguradora" size="60" readonly="" value="<?=$nomSeguradora;?>"> &nbsp;
                <input type="hidden" name="HdncodSeguradora" value="<?=$codSegura;?>">
                <a href="javascript:procurarCgm('frm', 'codSeguradora', 'nomSeguradora', 'juridica', '<?=Sessao::getId()?>');">
                <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar seguradora" border=0 align="absmiddle">

            </td>
        </tr>
<?php
 geraCampoData2("*Data de Vencimento", "dtVencimento", $dtVencimento, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\"onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (!verificaData(this)) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');this.value='';};\"","Informe a data de vencimento da apólice","Buscar data de vencimento" );
?>
        <tr>
            <td class="label" title="Dados do contato (nome, telefone, etc)">*Contato</td>
            <td class="field">
                <input type="text" name="contato" size="40" maxlength="40" value="<?=$contato;?>">
            </td>
        </tr>
        <tr>
            <td class="field" colspan="2">
                <?php geraBotaoOk(); ?>
            </td>
        </tr>

        </table>

        </form>
<?php
        break;

        // insere apolice no BD
        case 1:

        $Apolice->numero        = $numero;
        $Apolice->codSeguradora = $codSeguradora;
        $Apolice->dtVencimento  = $dtVencimento;
        $Apolice->contato       = $contato;
        $seguradora = $nomSeguradora;

        if (comparaValor("num_apolice", $numero,"patrimonio.apolice", "and numcgm = $codSeguradora")) {
            if ($Apolice->incluiApolice()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $Apolice->numero);
                $audicao->insereAuditoria();
                alertaAviso($PHP_SELF,"Apólice ".$numero." - ".$seguradora,"incluir","aviso",Sessao::getId());
            } else
                alertaAviso("listaApolice.php","Apólice ".$numero." - ".$sessao,"n_incluir","erro",Sessao::getId());
        } else {
            echo '
                <script type="text/javascript">
                alertaAviso("O Número: '.$numero.' desta Apólice, já está cadastrada na Seguradora: '.$seguradora.'","unica","erro", "'.Sessao::getId().'");            mudaTelaPrincipal("incluiApolice.php?'.Sessao::getId().'&numero='.$numero.'&nomSeguradora='.$seguradora.'&dtVencimento='.$dtVencimento.'&contato='.$contato.'&codSeguradora='.$codSeguradora.'"); </script>';
        }
        break;
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
