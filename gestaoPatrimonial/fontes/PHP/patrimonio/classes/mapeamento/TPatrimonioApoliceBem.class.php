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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26218 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-19 12:40:22 -0200 (Sex, 19 Out 2007) $

    * Casos de uso: uc-03.01.08
                    uc-03.01.06
*/

/*
$Log$
Revision 1.2  2007/10/05 13:00:16  hboaventura
inclusão dos arquivos

Revision 1.1  2007/09/18 15:10:55  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioApoliceBem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioApoliceBem()
{
    parent::Persistente();
    $this->setTabela('patrimonio.apolice_bem');
    $this->setCampoCod('cod_apolice');
    $this->setComplementoChave( 'cod_bem' );
    $this->AddCampo('cod_apolice','integer',true,'',true,true);
    $this->AddCampo('cod_bem','integer',true,'',true,true);

}

    public function recuperaMaxApoliceBem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxApoliceBem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMaxApoliceBem()
    {
        $stSql = "
            SELECT apolice_bem.cod_bem
                 , apolice_bem.cod_apolice
              FROM patrimonio.apolice_bem
        INNER JOIN ( SELECT cod_bem
                          , MAX(timestamp) AS timestamp
                       FROM patrimonio.apolice_bem
                   GROUP BY cod_bem
                   ) AS apolice_bem_max
                ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
               AND apolice_bem_max.timestamp = apolice_bem.timestamp
             WHERE apolice_bem.cod_bem = ".$this->getDado('cod_bem')."
        ";

        return $stSql;
    }
    public function recuperaBemApolice(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaBemApolice",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaBemApolice()
    {
        $stSql = "
            SELECT bem.cod_bem
                 , bem.cod_natureza
                 , bem.cod_grupo
                 , bem.cod_especie
                 , bem.descricao
                 , apolice_bem.cod_apolice
              FROM patrimonio.apolice_bem
        INNER JOIN ( SELECT cod_bem
                          , MAX(timestamp) AS timestamp
                       FROM patrimonio.apolice_bem
                   GROUP BY cod_bem
                   ) AS apolice_bem_max
                ON apolice_bem_max.cod_bem = apolice_bem.cod_bem
               AND apolice_bem_max.timestamp = apolice_bem.timestamp
        INNER JOIN patrimonio.bem
                ON bem.cod_bem = apolice_bem.cod_bem
             WHERE ";
        if ( $this->getDado('cod_bem') ) {
            $stSql .= " apolice_bem.cod_bem = ".$this->getDado('cod_bem')." AND   ";
        }
        if ( $this->getDado('cod_apolice') ) {
            $stSql .= " apolice_bem.cod_apolice = ".$this->getDado('cod_apolice')." AND   ";
        }

        return substr($stSql,0,-6);
    }

}
