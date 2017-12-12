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
* Arquivo de instância para TipoTratamento
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.91
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
//Formulário em HTML para entrada de dados
case 0:
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = f.codClassificacao.value;
            if (campo=="xxx") {
                mensagem += "@Campo Tratamento inválido!()";
                erro = true;
            }

        campo = f.nomTratamento.value.length;
            if (campo==0) {
                mensagem += "@Campo Tratamento inválido!()";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        document.frm.ok.disabled = true;
        if (Valida()) {
            document.frm.submit();
        } else {
            document.frm.ok.disabled = false;
        }
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='oculto'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Tratamento
        </td>
    </tr>
    <tr>
        <td class='label' width='20%' rowspan="2" title="Descrição do tratamento">
            *Tratamento
        </td>
        <td class='field' width='80%' >
            <input type="text" name="codTxtClassificacao" size="5" maxlength="5" onchange="JavaScript: preencheCampo(this, document.frm.codClassificacao);">
            <select name="codClassificacao" onchange="JavaScript: preencheCampo(this, document.frm.codTxtClassificacao);">
                <option value=xxx>Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$select =   "SELECT
            cod_classificacao,
            nom_classificacao
            FROM
            cse.classificacao_tratamento
            WHERE
            cod_classificacao > 0
            order by nom_classificacao";
//echo $select."<br>";
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $codClass = $dbConfig->pegaCampo("cod_classificacao");
    $nomClass = $dbConfig->pegaCampo("nom_classificacao");
    $dbConfig->vaiProximo();
    $lista .= "                <option value=".$codClass;
    $lista .= ">".$nomClass."</option>;\n";
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
echo $lista;
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class='field' width='80%'>
            <input type='text' name='nomTratamento' value='' size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
        </td>
    </tr>
    <tr>
        <td colspan='2' class='field'>
            <?php geraBotaoOk(); ?>
        </td>
    </tr>
</table>
</form>
<?php
    break;

//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_tratamento", $nomTratamento,"cse.tipo_tratamento"," And cod_classificacao = '".$codClassificacao."' ",1)) {
        $js .= "mensagem += '@O nome ".$nomTratamento." já existe'; \n";
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $cse = new cse();

        $objeto = $nomTratamento;
        $var["nomTratamento"] = $nomTratamento;
        $var["codClassificacao"] = $codClassificacao;
        if ($cse->incluirTipoTratamento($var) ) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            sistemaLegado::alertaAviso($PHP_SELF,$objeto,"incluir","aviso","");
        } else {
            sistemaLegado::exibeAviso($objeto,"n_incluir","erro");
            $js .= "f.ok.disabled = false; \n";
        }
    } else {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    }
    break;

}//Fim switch

sistemaLegado::executaFrameOculto($js);
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
