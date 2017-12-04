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
    * Classe de mapeamento da tabela ima.codigo_dirf
    * Data de Criação: 16/01/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMACodigoDirf extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMACodigoDirf()
    {
        parent::Persistente();
        $this->setTabela("ima.codigo_dirf");

        $this->setCampoCod('cod_dirf');
        $this->setComplementoChave('exercicio,tipo');

        $this->AddCampo('exercicio','char'    ,true  ,'4'  ,true,false);
        $this->AddCampo('cod_dirf' ,'sequence',true  ,''   ,true,false);
        $this->AddCampo('tipo'     ,'char'    ,true  ,'1'  ,true,false);
        $this->AddCampo('descricao','varchar' ,true  ,'250',false,false);

    }

    public function recuperaCodigosDIRF(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCodigosDIRF",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaCodigosDIRF()
    {
        $stSql .= "SELECT *                                                                      \n";
        $stSql .= "     , ( CASE WHEN tipo = 'F' THEN 'Pessoa Física'                            \n";
        $stSql .= "              WHEN tipo = 'J' THEN 'Pessoa Jurídica' END ) as tipo_formatado  \n";
        $stSql .= "  FROM ima.codigo_dirf                                                        \n";

        return $stSql;
    }
}
?>
