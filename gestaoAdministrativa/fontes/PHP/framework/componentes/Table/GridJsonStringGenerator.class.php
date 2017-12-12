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
    * Componente Grid
    * Data de Criação   : 23/03/2007

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.1  2007/06/13 21:27:08  domluc
*** empty log message ***

*/

require_once 'Table.class.php';

/**
 * class Grid
 */
class Grid
{
    /**
    * Url/Arquivo acessado para retornar os dados
    */
    public $stUrl;

    /**
    * Recordset com os Dados
    */
    public $rsDataSource;

    public function Grid($stUrl = null)
    {
        $this->stUrl = $stUrl;

    }

    public function show()
    {
    }

} // end of Grid
?>
