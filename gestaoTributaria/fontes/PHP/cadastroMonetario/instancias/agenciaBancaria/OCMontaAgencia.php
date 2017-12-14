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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * $Id: OCMontaAgencia.php 64131 2015-12-04 21:03:54Z jean $

    * Casos de uso: uc-05.05.02
                    uc-03.03.05
*/

/*
$Log$
Revision 1.4  2006/10/09 08:48:56  souzadl
Coloquei o código da case dentro de uma função e chamei essa função dentro do case. Fiz isso para poder utilizar essa função em outro oculto a fim de preencher os dados do componente.

Revision 1.3  2006/09/15 14:57:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

Revision 1.2  2006/09/12 17:43:44  fabio
correção de caso de uso

Revision 1.1  2006/09/12 16:39:19  cercato
correcao do include do componente e adicao do oculto para o componente agencia.

Revision 1.1  2006/07/13 13:00:38  leandro.zis
Componente IMontaAgencia

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function PreencheAgencia(Request $request)
{
    if ($request == "") {
        $arRequest = $_REQUEST;
    } else {
        $arRequest = $request->getAll();
    }

    $stJs .= "limpaSelect(f.stNumAgencia,1); \n";
    $stJs .= "f.stNumAgenciaTxt.value = ''; \n";
    $stJs .= ' d.getElementById(\'stNumAgencia\').value = \'\';';

    if ($arRequest['stNumBanco']) {
        $rsBanco = new RecordSet;
        $rsAgencia = new RecordSet;
        include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php" );
        $obTMONBanco = new TMONBanco;
        $stFiltro = ' where num_banco = \''.$arRequest['stNumBanco'].'\'';
        $obTMONBanco->recuperaTodos($rsBanco, $stFiltro);

        if ($rsBanco->getCampo('cod_banco') ) {
            include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php" );
            $obTMONAgencia = new TMONAgencia;
            $stFiltro = ' where cod_banco = '.$rsBanco->getCampo('cod_banco');
            if ($_GET['boVinculoPlanoBanco']) {
                $stFiltro .= " AND EXISTS ( SELECT 1
                                              FROM contabilidade.plano_banco
                                             WHERE plano_banco.cod_banco = agencia.cod_banco
                                               AND plano_banco.cod_agencia = agencia.cod_agencia
                                               AND plano_banco.exercicio = '".Sessao::getExercicio()."') ";
            }
            $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);
        }
        $inCount = 1;

        $stJs .= "f.stNumAgencia.options[0] = new Option('Selecione','', 'selected');\n";

        while (!$rsAgencia->eof()) {
            $inId   = $rsAgencia->getCampo("num_agencia");
            $stDesc = $rsAgencia->getCampo("nom_agencia");

            $stJs .= "f.stNumAgencia.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsAgencia->proximo();
            $inCount++;
        }
    }

    return $stJs;
}

switch ($request->get("stCtrl")) {
   case "PreencheAgencia":
        $stJs .= PreencheAgencia($request);
   break;
}

if( $stJs)
    echo $stJs;
