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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

$mascaraSetor = pegaConfiguracao('mascara_local',2);

?>

<tr>
        <td class="label" rowspan="6" title="Local da impressora">*Local</td>
        <td class="field">
            <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>" onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"  onChange="JavaScript: preencheCA( 'codMasSetor', this.value )">
        </td>
    </tr>
    <tr>
        <td class=field>
        <select name=codOrgao onChange="javascript: preencheCA('codOrgao', this.value);" style="width:400px">
            <option value=xxx SELECTED>Selecione</option>
    <?php
    //Faz o combo de Órgãos
    $sSQL = "SELECT cod_orgao, nom_orgao, ano_exercicio FROM administracao.orgao ORDER by nom_orgao";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboCodOrgao = "";

    while (!$dbEmp->eof()) {
    $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
    $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
    $anoE       = trim($dbEmp->pegaCampo("ano_exercicio"));
    $chave = $codOrgaof."-".$anoE;
    $dbEmp->vaiProximo();
    $comboCodOrgao .= "         <option value='".$chave."'";
    if (isset($codOrgao)) {
        if ($chave == $codOrgao) {
            $comboCodOrgao .= " SELECTED";
            $nomOrgao = $nomOrgaof;
        }
    }
    $comboCodOrgao .= ">".$nomOrgaof." - ".$anoE."</option>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $comboCodOrgao;
    ?>
        </select>
        <input type="hidden" name="nomOrgao" value="">
        </td>
    </tr>
    <tr>
        <td class=field>
            <select name=codUnidade onChange="javascript: preencheCA('codUnidade', this.value);" style="width:400px">
                <option value=xxx SELECTED>Selecione</option>
            </select>
            <input type="hidden" name="nomUnidade" value="">
        </td>
    </tr>
    <tr>
        <td class=field>
            <select name=codDepartamento onChange="javascript: preencheCA('codDepartamento', this.value);" style="width:400px">
                <option value=xxx SELECTED>Selecione</option>
            </select>
            <input type="hidden" name="nomDepartamento" value="">
        </td>
    </tr>
    <tr>
        <td class=field>
            <select name=codSetor onChange="javascript: preencheCA('codSetor', this.value);" style="width:400px">
                <option value=xxx SELECTED>Selecione</option>
            </select>
            <input type="hidden" name="nomSetor" value="">
            <input type="hidden" name="anoExercicioSetor" value="">
        </td>
    </tr>
    <tr>
        <td class=field>
            <select name=codLocal onChange="javascript: preencheCA('codLocal', this.value);" style="width:400px">
                <option value=xxx SELECTED>Selecione</option>
            </select>
            <input type="hidden" name="nomLocal" value="">
            <input type="hidden" name="anoExercicioLocal" value="">
        </td>
    </tr>
    <?php
        if ($codMasSetor != "") {
                echo "
                    <script type='text/javascript'>
                            preencheCA('codMasSetor', '".$codMasSetor."');
                    </script>
                ";
        }
    ?>
