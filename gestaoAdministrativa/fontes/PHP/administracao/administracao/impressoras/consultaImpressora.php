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
    * Manutneção de impressoras
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * Casos de uso: uc-01.03.92

    $Id: consultaImpressora.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
 include(CAM_FW_LEGADO."funcoesLegado.lib.php"  );
 include(CAM_FW_LEGADO."usuarioLegado.class.php");
 include(CAM_FW_LEGADO."mascarasLegado.lib.php" );

 setAjuda( "UC-01.03.92" );

 $stMascaraLocal = pegaConfiguracao("mascara_local");

?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>

    <table width='100%' id="impressoras">
        <tr>
            <td class='alt_dados' colspan='3'>Impressoras disponíveis</td>
        </tr>
        <tr>
            <td class='labelleftcabecalho' width='2%'>&nbsp;</td>
            <td class='labelleftcabecalho' width='28%'>Impressora</td>
            <td class='labelleftcabecalho' width='70%'>Local</td>
        </tr>
<?php
    $imgStatus = "";
    $sSQL = "
            SELECT  *
              FROM  administracao.impressora
             WHERE  cod_impressora > 0
          ORDER BY  nom_impressora ";

    $conectaBD = new dataBaseLegado;
    $conectaBD->abreBD();
    $conectaBD->abreSelecao($sSQL);
    $conectaBD->fechaBD();
    $conectaBD->vaiPrimeiro();
    while (!$conectaBD->eof()) {
        $usuario = new usuarioLegado;
        if ($usuario->verificaUsuarioImpressora(Sessao::read('numCgm'),$conectaBD->pegaCampo("cod_impressora"))) {
            $imgStatus = "<img src='".CAM_FW_IMAGENS."btnselecionar.png' border='0'>";
        } else {
            $imgStatus = "<img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'>";
        }

        $select = "SELECT  descricao as nom_local
                     FROM  organograma.local
                    WHERE  cod_local = ".$conectaBD->pegaCampo("cod_local");

        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomLocal = $dbConfig->pegaCampo("nom_local");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
        <tr>
            <td class='show_dados' width='2%'><?=$imgStatus;?></td>
            <td class='show_dados' width='28%'><?=$conectaBD->pegaCampo("nom_impressora");?></td>
            <td class='show_dados' width='70%'><?=$nomLocal;?></td>
        </tr>
<?php
        $conectaBD->vaiProximo();
    }
    $conectaBD->limpaSelecao();
?>

        <script>zebra('impressoras','zb');</script>
