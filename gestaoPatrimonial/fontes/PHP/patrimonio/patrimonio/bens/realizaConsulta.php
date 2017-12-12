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
    * Arquivo que consulta por código
    * Data de Criação   : 27/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.9  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:27  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../consulta.class.php';
    $cCod = new consulta;
    $cCod->setaVariaveisBens($codkey, $natu, $grup, $espec);
    if ($cod != "") {
        $cLista = $cCod->consultaCodBem();

        if ($cLista!="") {
            echo "<table width=70%>";
            while (list ($key, $val) = each ($cLista)) {
                echo "<tr><td class=show_dados width=100%>$val</td>
                <td class=show_dados><a href='detalhesBem.php?".Sessao::getId()."'><img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title='Alterar' border='0'></a></td>"; //Direciona para a página alteraManutencao2.php
            }
            echo "</table>";
        }
    }
    if ($natu != "" && $grup != 0 && $espec != 0) {
        $clLista = $cCod->consultaClass();

        if ($clLista!="") {
            echo "<table width=70%>";
            while (list ($key, $val) = each ($clLista)) {
                echo "<tr><td class=show_dados width=100%>$val</td>
                <td class=show_dados><a href='detalhesBem.php?".Sessao::getId()."'><img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title='Alterar' border='0'></a></td>"; //Direciona para a página alteraManutencao2.php
            }
            echo "</table>";
        }
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
