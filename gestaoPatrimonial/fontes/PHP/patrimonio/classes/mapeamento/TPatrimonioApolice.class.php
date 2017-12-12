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
*/

/*
$Log$
Revision 1.3  2007/10/17 13:26:42  hboaventura
correção dos arquivos

Revision 1.2  2007/10/05 13:00:16  hboaventura
inclusão dos arquivos

Revision 1.1  2007/09/18 15:10:55  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioApolice extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TPatrimonioApolice()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.apolice');
        $this->setCampoCod('cod_apolice');
        $this->AddCampo('cod_apolice'    , 'integer', true , ''    ,true ,false);
        $this->AddCampo('numcgm'         , 'integer', true , ''    ,false,true);
        $this->AddCampo('num_apolice'    , 'varchar', true , '15'  ,false,false);
        $this->AddCampo('dt_vencimento'  , 'date'   , true , ''    ,false,false);
        $this->AddCampo('contato'        , 'varchar', true , '40'  ,false,false);
        $this->AddCampo('dt_assinatura'  , 'date'   , false, ''    ,false,false);
        $this->AddCampo('inicio_vigencia', 'date'   , false, ''    ,false,false);
        $this->AddCampo('valor_apolice'  , 'numeric', false, '14,2',false,false);
        $this->AddCampo('valor_franquia' , 'numeric', false, '14,2',false,false);
        $this->AddCampo('observacoes'    , 'text'   , false, ''    ,false,false);
        $this->AddCampo('nome_arquivo'   , 'varchar', false , '80' ,false,false);

    }

    public function recuperaSeguradoras(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaSeguradoras",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaSeguradoras()
    {
        $stSql = "
            SELECT apolice.numcgm as num_seguradora
                 , sw_cgm.nom_cgm as nom_seguradora
--    		     , apolice.contato
                FROM patrimonio.apolice
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = apolice.numcgm
          GROUP BY apolice.numcgm
                 , sw_cgm.nom_cgm
--  		  		 , apolice.contato
        ";

        return $stSql;
    }

    public function recuperaApoliceSeguradora(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaApoliceSeguradora",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaApoliceSeguradora()
    {
        $stSql = "
            SELECT apolice.cod_apolice
                 , apolice.num_apolice
                 , TO_CHAR( apolice.dt_vencimento,'dd/mm/yyyy' ) AS dt_vencimento
              FROM patrimonio.apolice
             WHERE ";
        if ( $this->getDado( 'numcgm' ) ) {
            $stSql.= " apolice.numcgm = ".$this->getDado( 'numcgm' )." AND   ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaApolices(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaApolices",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaApolices()
    {
        $stSql = "
            SELECT apolice.cod_apolice
                 , apolice.num_apolice
                 , TO_CHAR( apolice.dt_vencimento,'dd/mm/yyyy' ) AS dt_vencimento
                 , apolice.numcgm AS num_seguradora
                 , sw_cgm.nom_cgm AS nom_seguradora
                 , apolice.contato
                 , TO_CHAR( apolice.dt_assinatura,'dd/mm/yyyy' ) AS dt_assinatura
                 , TO_CHAR( apolice.inicio_vigencia,'dd/mm/yyyy' ) AS inicio_vigencia
                 , apolice.valor_apolice
                 , apolice.valor_franquia
                 , apolice.observacoes
                 , apolice.nome_arquivo
              FROM patrimonio.apolice
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = apolice.numcgm
             WHERE ";
        if ( $this->getDado('cod_apolice') ) {
            $stSql .= " apolice.cod_apolice = ".$this->getDado('cod_apolice')." AND   ";
        }

        return substr($stSql,0,-6);
    }

}
