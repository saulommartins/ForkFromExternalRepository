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
* Arquivo de instância para manutenção de Cidadão
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 4230 $
$Name$
$Author: lizandro $
$Date: 2005-12-22 11:18:34 -0200 (Qui, 22 Dez 2005) $

* Casos de uso: uc-01.07.97
*/

$titulo = "Procura Cidadão";
  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"   );
if (!(isset($ctrl))) {
    $ctrl = 0;
    unset($sessao->transf1);
}

if (isset($pagina)) {
    $flag = 1;
}

switch ($ctrl) {
    case 0:{
        echo
        "<script type='text/javascript'>
            function Salvar()
            {
               document.frm.flag.value=1;
               document.frm.submit();
            }
        </script>
        <form name='frm' action='procuraCidadao.php?".$sessao->id."' method='post'>
            <table width='100%'>
                <tr>
                    <td class=alt_dados colspan='2'>
                        Dados do filtro
                    </td>
                </tr>
                <tr>
                    <td class=label width='20%' title='Nome do Cidadão'>
                        Nome
                    </td>
                    <td class=field width='80%'>
                        <input type='text' name='nomCidadao' size='60' maxlength='200'>
                        <input type='hidden' name='flag' value='0'>
                        <input type='hidden' name='nomForm' value='".$nomForm."'>
                    </td>
                </tr>
                <tr>
                    <td class=label title='RG do Cidadão'>
                         RG
                    </td>
                    <td class=field>
                        <input type='text' name='rg' size='15' maxlength='11'>
                    </td>
                </tr>
                <tr>
                    <td class=label title='Data Nascimento cidadão'>
                         Data de Nascimento
                    </td>
                    <td class=field>
                        <input type=\"text\" maxlength=\"10\" size=\"10\" name=\"dataNasc\" value=\"\"
                        onKeyPress=\"return(isValido(this, event, '0123456789'));\"
                        onKeyUp=\"mascaraData(this, event);\"
                        onBlur=\"JavaScript: if ( !verificaData(this) ) { this.value='';};\">
                    </td>
                </tr>";
        echo "<tr>
                 <td class=field colspan='2'>";
        echo		geraBotaoOk();
        echo "	</td>
             </tr>
        </table>
        </form>";

        if ($flag == 1) {
            echo
            "<script type='text/javascript'>
                function Insere(codCidadao,nomCidadao)
                {
                    var sCodCidadao;
                    var sNomCidadao;
                    sCodCidadao = codCidadao;
                    sNomCidadao = nomCidadao;
                    window.opener.parent.frames['telaPrincipal'].document.".$nomForm.".codCidadao.value = sCodCidadao;
                    window.opener.parent.frames['telaPrincipal'].document.".$nomForm.".nomCidadao.value = sNomCidadao;
                    window.close();
                }
            </script>";

        $sqlAux = "";

        $sqlAux = "cod_cidadao > 0";
        if ($codCidadao != "") {
               $sqlAux .= " AND cod_cidadao = ".$codCidadao ;
        }

        if ($nomCidadao != "") {
               $sqlAux .= " AND lower(nom_cidadao) LIKE lower('%".$nomCidadao."%')";
        }
        if ($rg != "") {
               $sqlAux .= " AND num_rg = '".$rg."'";
        }
        if ($dataNasc != "") {
               $sqlAux .= " AND dt_nascimento = '".$dataNasc."'";
        }

        $select  = "";

        $select .= "SELECT
                       C.cod_cidadao,
            C.nom_cidadao
            FROM cse.cidadao AS C
            WHERE ".$sqlAux;

        if (!(isset($sessao->transf1))) {
               $sessao->transf1 = $select;
        }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf1,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento =
        "&nomForm=".$nomForm."&campoCodCidadao=".$campoCodCidadao."&campoNom=".$campoNomCidadao;
        $paginacao->geraLinks();
        $paginacao->pegaOrder("C.nom_cidadao","ASC");
        $sSQL  = $paginacao->geraSQL();
        $count = $paginacao->contador();

        $conn = new dataBaseLegado;
        $conn->abreBd();
        $conn->abreSelecao($sSQL);
        echo
        "<table width='100%'>
        <tr>
        <td class=labelcenter>
            &nbsp;
        </td>
        <td class=labelcenter>
            Código
        </td>
        <td class=labelcenter>
            Nome
        </td>
        <td class=labelcenter>
            &nbsp;
        </td>
        </tr>";
        if ($conn->numeroDeLinhas > 0) {
                while (!($conn->eof())) {
            $codCidadao = $conn->pegaCampo("cod_cidadao");
            $nomCidadao = $conn->pegaCampo("nom_cidadao");
            echo
            "<tr>
                <td class=labelcenter width='5%'>
                ".$count++."
                </td>
                <td class=show_dados_right width='10%'>
                ".$codCidadao."
                </td>
                <td class=show_dados>
                ".$nomCidadao."
                </td>
                <td class=show_dados_center width='10%'>
                <a href='#' onclick=\"Insere('".$codCidadao."','".$nomCidadao."');\">

                    <img src='".CAM_FW_IMAGENS."btnselecionar.png' alt='Selecionar' width=22 height=22 border=0>
                </a>
                </td>
            </tr>";
            $conn->vaiProximo();
                }
        } else {
        echo "	<tr>
                <td class=show_dados_center colspan='4'>
                Nenhum Cidadão encontrado
                </td>
            </tr>";
        }
        $conn->limpaSelecao();
        $conn->fechaBd();
        echo "<table width=500 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
        break;
        }
    }
}
