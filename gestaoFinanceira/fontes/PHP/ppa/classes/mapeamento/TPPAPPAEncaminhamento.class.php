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
    * Classe de mapeamento PPA.PPA_ENCAMINHAMENTO
    * Data de Criação: 03/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.12
*/

class TPPAPPAEncaminhamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.ppa_encaminhamento');

        $this->setCampoCod('cod_ppa');
        $this->setComplementoChave('');

        $this->AddCampo('cod_ppa'           , 'integer'  , true, '' , true , true);
        $this->AddCampo('cod_periodicidade' , 'integer'  , true, '' , false, true);
        $this->AddCampo('dt_encaminhamento' , 'date'     , true, '' , false, false);
        $this->AddCampo('dt_devolucao'      , 'date'     , true, '' , false, false);
        $this->AddCampo('nro_protocolo'     , 'varchar'  , true, '9', false, false);
        $this->AddCampo('timestamp'         , 'timestamp', true, '' , false, false);
    }

    public function recuperaPPAEncaminhamento(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPPAEncaminhamento", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPPAEncaminhamento()
    {
        $stSql = "	   SELECT ppa_encaminhamento.cod_ppa										        \n";
        $stSql.= "		 	, ppa_encaminhamento.cod_periodicidade 	       					            \n";
        $stSql.= "		 	, ppa_encaminhamento.dt_encaminhamento 							            \n";
        $stSql.= "		 	, ppa_encaminhamento.dt_devolucao           						        \n";
        $stSql.= "		 	, ppa_encaminhamento.nro_protocolo           						        \n";
        $stSql.= "		 	, periodicidade.nom_periodicidade               	                        \n";
        $stSql.= "		 FROM ppa.ppa_encaminhamento    						                        \n";
        $stSql.= " INNER JOIN ppa.ppa       									                        \n";
        $stSql.= " 		   ON ppa.cod_ppa = ppa_encaminhamento.cod_ppa 							        \n";
        $stSql.= " INNER JOIN ppa.periodicidade     							                        \n";
        $stSql.= " 		   ON periodicidade.cod_periodicidade = ppa_encaminhamento.cod_periodicidade 	\n";

        //echo $stSql;
        return $stSql;
    }

    /**
     * Método que constroi a string SQL para o metodo getMaxEncaminhamento
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function getMaxTimestamp(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT ppa_encaminhamento.cod_ppa
                 , TO_CHAR(ppa_encaminhamento.dt_encaminhamento,'dd/mm/yyyy') AS dt_encaminhamento
                 , TO_CHAR(ppa_encaminhamento.dt_devolucao,'dd/mm/yyyy') AS dt_devolucao
                 , ppa_encaminhamento.nro_protocolo
                 , MAX(ppa_encaminhamento.timestamp) AS timestamp
                 , ppa.ano_inicio
                 , ppa.ano_final
              FROM ppa.ppa_encaminhamento
        INNER JOIN ( SELECT cod_ppa
                          , MAX(timestamp) AS timestamp
                       FROM ppa.ppa_encaminhamento
                   GROUP BY cod_ppa
                   ) AS max_ppa_encaminhamento
                ON ppa_encaminhamento.timestamp = max_ppa_encaminhamento.timestamp
               AND ppa_encaminhamento.cod_ppa   = max_ppa_encaminhamento.cod_ppa
        INNER JOIN ppa.ppa
                ON ppa_encaminhamento.cod_ppa   = ppa.cod_ppa
             WHERE ppa_encaminhamento.cod_ppa = " . $this->getDado('cod_ppa') . "
          GROUP BY ppa_encaminhamento.cod_ppa
                 , ppa_encaminhamento.dt_encaminhamento
                 , ppa_encaminhamento.dt_devolucao
                 , ppa_encaminhamento.nro_protocolo
                 , ppa.ano_inicio
                 , ppa.ano_final
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
 } // end of class
