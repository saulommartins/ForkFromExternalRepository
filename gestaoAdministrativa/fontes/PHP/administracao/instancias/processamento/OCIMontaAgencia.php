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

    * Casos de uso: uc-03.03.05
*/

/*
$Log$
Revision 1.1  2006/07/13 13:00:38  leandro.zis
Componente IMontaAgencia

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST["stCtrl"]) {
   case "PreencheAgencia":
       $stJs .= "limpaSelect(f.stNumAgencia,1); \n";
       $stJs .= "f.stNumAgenciaTxt.value = ''; \n";
       $stJs .= ' d.getElementById(\'stNumAgencia\').value = \'\';';
       if ($_GET['stNumBanco']) {
          $rsBanco = new RecordSet;
          $rsAgencia = new RecordSet;
          include_once ( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php" );
          $obTMONBanco = new TMONBanco;
          $stFiltro = ' where num_banco = \''.$_GET['stNumBanco'].'\'';
          $obTMONBanco->recuperaTodos($rsBanco, $stFiltro);
          if ($rsBanco->getCampo('cod_banco') ) {
             include_once ( CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php" );
             $obTMONAgencia = new TMONAgencia;
             $stFiltro = ' where cod_banco = '.$rsBanco->getCampo('cod_banco');
             $obTMONAgencia->recuperaTodos($rsAgencia, $stFiltro);
          }
          $inCount = 1;

          //  if ($rsAgencia->getNumLinhas()>1) {
                $stJs .= "f.stNumAgencia.options[0] = new Option('Selecione','', 'selected');\n";
          //  }
          /*  elseif ($rsAgencia->getNumLinhas()==1) {
                $inCount--;
                $stJs .= "f.stNumAgencia.value= '".$rsAgencia->getCampo('num_agencia')."';\n";
                $stJs .= "f.stNumAgenciaTxt.value= '".$rsAgencia->getCampo('num_agencia')."';\n";
            }*/
            while (!$rsAgencia->eof()) {
                $inId   = $rsAgencia->getCampo("num_agencia");
                $stDesc = $rsAgencia->getCampo("nom_agencia");

                $stJs .= "f.stNumAgencia.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsAgencia->proximo();
                $inCount++;
            }

       }
   break;
}

if( $stJs)
    echo $stJs;
