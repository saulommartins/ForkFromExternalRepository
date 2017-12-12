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
* Manutneção do sistema
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.91
*/

     include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
     include (CAM_FW_LEGADO."funcoesLegado.lib.php");
     include (CAM_FW_LEGADO."auditoriaLegada.class.php");
     include (CAM_FW_LEGADO."mascarasLegado.lib.php");

if (!(isset($controle)))
$controle = 0;

switch ($controle) {
case 0:

if ((!(isset($titulo))) and (!(isset($texto)))) {
$titulo = "";
$texto = "";
}
/*
if (($texto == "") and ($titulo == "")) {
    if (($codOrgaos == "xxx") and ($codUnidade == "xxx") and ($codDepartamento == "xxx") and ($codSetor == "xxx")) {
    $sSQL = "SELECT * FROM administracao.comunicado WHERE cod_orgao = 0 AND cod_departamento = 0 AND cod_unidade = 0
            AND cod_setor = 0
            AND exercicio = '0000'";
            echo $sSQL;
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            while (!$dbEmp->eof()) {
                $titulo = trim($dbEmp->pegaCampo("titulo"));
                $texto = trim($dbEmp->pegaCampo("texto"));
                $dbEmp->vaiProximo();

        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

    }
    if ((!(isset($codOrgaos))) and (!(isset($codUnidade))) and (!(isset($codDepartamento))) and (!(isset($codSetor)))) {
            $sSQL = "SELECT * FROM administracao.comunicado WHERE cod_orgao = 0 AND cod_departamento = 0 AND cod_unidade = 0
            AND cod_setor = 0
            AND exercicio = '0000'";
            //echo $sSQL;
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            while (!$dbEmp->eof()) {
                $titulo = trim($dbEmp->pegaCampo("titulo"));
                $texto = trim($dbEmp->pegaCampo("texto"));
                $dbEmp->vaiProximo();

        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

    }

    if ((isset($codOrgaos)) and (isset($codUnidade)) and (isset($codDepartamento)) and (isset($codSetor))) {
        if (($codOrgao != "xxx") and ($codUnidade != "xxx") and ($codDepartamento != "xxx") and ($codSetor != "xxx")) {
        $sSQL = "SELECT * FROM administracao.comunicado WHERE cod_orgao = ".$codOrgaos." AND cod_departamento = ".$codDepartamento." AND cod_unidade = ".$codUnidade."
                AND cod_setor = ".$codSetor."
                AND exercicio = '".Sessao::getExercicio()."'";
                //echo $sSQL;
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                while (!$dbEmp->eof()) {
                    $titulo = trim($dbEmp->pegaCampo("titulo"));
                    $texto = trim($dbEmp->pegaCampo("texto"));
                    $dbEmp->vaiProximo();

            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

        }
    }
}
*/

?>
<script>
var ns6=document.getElementById&&!document.all

function restrictinput(maxlength,e,placeholder)
{
if (window.event&&event.srcElement.value.length>=maxlength)
return false
else if (e.target&&e.target==eval(placeholder)&&e.target.value.length>=maxlength) {
var pressedkey=/[a-zA-Z0-9\.\,\/]/ //detect alphanumeric keys
if (pressedkey.test(String.fromCharCode(e.which)))
e.stopPropagation()
}
}

function countlimit(maxlength,e,placeholder)
{
var theform=eval(placeholder)
var lengthleft=maxlength-theform.value.length
var placeholderobj=document.all? document.all[placeholder] : document.getElementById(placeholder)
if (window.event||e.target&&e.target==eval(placeholder)) {
if (lengthleft<0)
theform.value=theform.value.substring(0,maxlength)
placeholderobj.innerHTML=lengthleft
}
}

function displaylimit(theform,thelimit)
{
var limit_text='';
if (document.all||ns6)
document.write(limit_text)
if (document.all) {
eval(theform).onkeypress=function () { return restrictinput(thelimit,event,theform)}
eval(theform).onkeyup=function () { countlimit(thelimit,event,theform)}
} else if (ns6) {
document.body.addEventListener('keypress', function (event) { restrictinput(thelimit,event,theform) }, true);
document.body.addEventListener('keyup', function (event) { countlimit(thelimit,event,theform) }, true);
}
}

</script>

<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.titulo.value.length;
            if (campo == 0) {
            mensagem += "@O Campo Título é obrigatório";
            erro = true;
         }

        campo = document.frm.texto.value.length;
            if (campo == 0) {
            mensagem += "@O campo Texto é obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {

            document.frm.action = "comunicado.php?<?=Sessao::getId();?>&controle=1";
            document.frm.submit();
         }
      }
      function Limpar()
      {
            document.frm.texto.value = "";
            document.frm.titulo.value = "";

      }
 </script>
<form name="frm" action="comunicado.php?<?=Sessao::getId();?>" method="POST">
<table width="100%">
<tr><td class="alt_dados" colspan=2>Dados para comunicado</td></tr>

<?php
    $title = "Informe o setor a que se destina o comunicado, ou deixe em branco para enviar a todos os setores";
    $obrigatorio = "false";
    include(CAM_FW_LEGADO."filtrosSELegado.inc.php");
?>

<tr>
<td class=label width="20%">*Título</td>
<td class=field width="80%"><input type="text" name="titulo" size=30 maxlength=30 value="<?=$titulo?>"></td>
</tr>

<tr>
<td class=label>*Texto</td>
<td class=field><textarea name='texto' cols='40' rows='3'><?=$texto?></textarea><br>
<script>
//displaylimit("document.frm.texto",300)
</script></td>
</tr>

<tr><td class=field colspan=2>
<?=geraBotaoOk();?>

</td></tr>
</table>
</form>

<?php
break;
case 1:

$sSQL = "SELECT numcgm FROM sw_cgm_pessoa_fisica WHERE numcgm = ".Sessao::read('numCgm');

$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
while (!$dbEmp->eof()) {
      $nuM = trim($dbEmp->pegaCampo("numcgm"));
      $dbEmp->vaiProximo();
}
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
if ($dbEmp->numeroDeLinhas == 0) {
echo '<script type="text/javascript">
                 alertaAviso("Somente pessoas físicas podem incluir um comunicado","unica","erro","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
exit;
}

$erro = 0;

if (($codOrgao == "xxx") and ($codUnidade == "xxx") and ($codDepartamento == "xxx") and ($codSetor == "xxx")) {
$erro = 1;

        $codComunicado = pegaID('cod_comunicado',"administracao.comunicado");
        $sql = "INSERT INTO administracao.comunicado (cod_comunicado, exercicio, cod_orgao, cod_unidade,
        cod_departamento, cod_setor, exercicio_setor, numcgm, titulo, texto) VALUES (".$codComunicado.", '0000',
        0,0,0,0,'0000',".Sessao::read('numCgm').",'".$titulo."','".$texto."')";
        //echo $sql;
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $titulo);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                 alertaAviso("'.$titulo.'","incluir","aviso","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
            } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$titulo.'","n_incluir","erro","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
            }
        $conn->fechaBD();
}

if (($codOrgaos != "xxx") and ($codUnidade != "xxx") and ($codDepartamento != "xxx") and ($codSetor != "xxx")) {
$erro = 1;

        $vet = preg_split( "/[^a-zA-Z0-9]/", $codMasSetor);
        $codOrgaos         = $vet[0];
        $codUnidade        = $vet[1];
        $codDepartamento   = $vet[2];
        $codSetor          = $vet[3];
        $anoExercicioSetor = $vet[4];

        $codComunicado = pegaID('cod_comunicado',"administracao.comunicado");
        $sql = "    INSERT INTO
                        administracao.comunicado
                    (
                        cod_comunicado,
                        exercicio,
                        cod_orgao,
                        cod_unidade,
                        cod_departamento,
                        cod_setor,
                        exercicio_setor,
                        numcgm,
                        titulo,
                        texto
                    )
                    VALUES
                    (
                        ".$codComunicado.",
                        '".Sessao::getExercicio()."',
                        ".$codOrgaos.",
                        ".$codUnidade.",
                        ".$codDepartamento.",
                        ".$codSetor.",
                        ".$anoExercicioSetor.",
                        ".Sessao::read('numCgm').",
                        '".$titulo."',
                        '".$texto."'
                    )";
        //echo $sql;
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $titulo);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                 alertaAviso("'.$titulo.'","incluir","aviso","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
            } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$titulo.'","n_incluir","erro","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
            }
        $conn->fechaBD();
}

if ($erros == 0) {
echo '<script type="text/javascript">
                 alertaAviso("Preencha corretamente o Órgão, Unidade, Departamente e Setor","unica","erro","'.Sessao::getId().'");
                 window.location = "comunicado.php?'.Sessao::getId().'";
                 </script>';
}
break;
case 100:
    include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
break;

}

     include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
