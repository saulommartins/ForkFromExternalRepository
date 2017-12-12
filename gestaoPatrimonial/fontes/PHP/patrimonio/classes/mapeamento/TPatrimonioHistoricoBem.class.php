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
    * Data de Criação: 12/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25841 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 10:02:21 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
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

class TPatrimonioHistoricoBem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPatrimonioHistoricoBem()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.historico_bem');
        $this->setCampoCod('cod_bem');
        $this->setComplementoChave( 'timestamp' );
        $this->AddCampo('cod_bem','integer',true,'',true,true);
        $this->AddCampo('cod_situacao','integer',true,'',false,true);
        $this->AddCampo('cod_local','integer',true,'',false,true);
        $this->AddCampo('cod_orgao','integer',true,'',false,true);
        $this->AddCampo('timestamp', 'timestamp', false, '', true , false );
        $this->AddCampo('descricao','varchar',true,'100',false,true);

    }

    public function recuperaLocalizacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaLocalizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaLocalizacao()
    {
        $stSql = "
            SELECT historico_bem.cod_bem
                 , historico_bem.cod_local
                 , historico_bem.cod_orgao
                 , to_char(now()::date,'YYYY') as ano_exercicio
              FROM patrimonio.historico_bem
             WHERE cod_bem = ".$this->getDado('cod_bem')."
               AND cod_local = ".$this->getDado('cod_local')."
               AND cod_orgao = ".$this->getDado('cod_orgao')."
               AND ano_exercicio = '".$this->getDado('ano_exercicio')."'
        ";

        return $stSql;
    }

    public function recuperaUltimaLocalizacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUltimaLocalizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaUltimaLocalizacao()
    {
        $stSql = "
            SELECT historico_bem.cod_bem
                 , historico_bem.cod_local
                 , historico_bem.cod_orgao
              FROM patrimonio.historico_bem
             WHERE cod_bem = ".$this->getDado('cod_bem')."
          ORDER BY timestamp desc
             LIMIT 1
        ";

        return $stSql;
    }

    public function recuperaUltimaLocalizacaoBem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stOrder = " ORDER BY cod_local ";

        return $this->executaRecupera("montaRecuperaUltimaLocalizacaoBem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaUltimaLocalizacaoBem()
    {
        $stSql = "
            SELECT
                 bem.num_placa
                ,bem.descricao
              FROM
                patrimonio.bem
        INNER JOIN
                patrimonio.historico_bem
                ON
                bem.cod_bem = historico_bem.cod_bem
        INNER JOIN
                ( SELECT cod_bem, max(timestamp) AS timestamp FROM patrimonio.historico_bem GROUP BY cod_bem ) AS bem10
                ON
                    historico_bem.cod_bem = bem10.cod_bem
                AND historico_bem.timestamp = bem10.timestamp
        ";

        return $stSql;
    }

}
