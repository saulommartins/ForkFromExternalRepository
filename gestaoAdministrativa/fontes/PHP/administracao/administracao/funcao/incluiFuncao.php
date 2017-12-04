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
* Manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include '../../includes/cabecalho.php';
if (!(isset($ctrl)))
$ctrl = 0;
switch ($ctrl) {
case 0:
?>
   <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomFuncao.value.length;
            if (campo == 0) {
            mensagem += "@O Campo Nome da Função é obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
   </script>

<form name="frm" action="incluiFuncao.php?<?=Sessao::getId();?>" method="POST">

<table width=450>

<tr>
<td colspan=2 class="alt_dados">Insira os dados da Função</td>
</tr>

<tr>
<td class=label>Nome da Função</td>
<td class=field><input type="text" name="nomFuncao" size=50>
<input type="hidden" name="ctrl" value=1>
</td>
</tr>

<tr>
<td class=field colspan=2><input type="button" name="Incluir" value="OK" style="width: 60px" onClick="Salvar();">&nbsp;
<input type="reset" value="Limpar" style="width: 60px">
</td>
</tr>

</table>

</form>

<?php
break;
case 1:
//*******************************************************************
// Faz a verficação se já não existem um regostro com esse nome
$sSQL = "select count(nom_funcao) as verifica from sw_funcao WHERE nom_funcao LIKE '".$nomFuncao."'";
$dbEmp = new dataBase;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$chaves  = trim($dbEmp->pegaCampo("verifica"));
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
//******************************************************************
if ($chaves ==  0) { //Verifica a exist^ncia de registros iguais
//******************************************************************
//Se não existir nenhum igual
    include '../../classes/configuracao.class.php';
    $nId = pegaID("cod_funcao","sw_funcao");
    $config = new configuracao;
    $config->setaValorFuncao($nId,$nomFuncao);
    if ($config->insertFuncao()) {
                    include '../../classes/auditoria.class.php';
                    $audicao = new auditoria;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomFuncao);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("'.$nomFuncao.'","incluir","aviso","'.Sessao::getId().'");
                    window.location = "incluiFuncao.php?'.Sessao::getId().'";
                    </script>';
                    } else {
                    echo '<script type="text/javascript">
                    alertaAviso("'.$nomFuncao.'","n_incluir","aviso","'.Sessao::getId().'");
                    window.location = "incluiFuncao.php?'.Sessao::getId().'";
                    </script>';
                    }

//******************************************************************
} else {
//******************************************************************
//Se já existir algum registro com esse nome
echo '<script type="text/javascript">
                    alertaAviso("Já existe uma função com esse nome","unica","erro","'.Sessao::getId().'");
                    window.location = "incluiClassificacao.php?'.Sessao::getId().'";
                    </script>';
}
//****************************************************************
break;
}
include '../../includes/rodape.php';
?>
