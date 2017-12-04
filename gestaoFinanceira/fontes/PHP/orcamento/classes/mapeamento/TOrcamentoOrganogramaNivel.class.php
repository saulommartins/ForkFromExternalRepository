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
 * Mapeamento da tabela orcamento.organograma_nivel
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 *
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TOrcamentoOrganogramaNivel extends Persistente
{
    /**
     * Método Construtor da classe TOrcamentoOrganogramaNivel
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('orcamento.organograma_nivel');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_organograma, cod_nivel');

        $this->AddCampo('cod_organograma' , 'integer', true, ''  , true , true );
        $this->AddCampo('cod_nivel'       , 'integer', true, ''  , true , true );
        $this->AddCampo('tipo'            , 'char'   , true, '1' , false, false);
    }

    /*
     * Método que constroi a string SQL para o metodo getMaxOrganogramaNivel
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
    public function getMaxOrganogramaNivel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT MAX(organograma_nivel.timestamp) AS timestamp
                 , organograma_nivel.cod_organograma
                 , organograma_nivel.cod_nivel
                 , organograma_nivel.tipo
              FROM orcamento.organograma_nivel
        INNER JOIN ( SELECT MAX(timestamp) AS timestamp
                          , cod_organograma
                          , tipo
                       FROM orcamento.organograma_nivel
                   GROUP BY cod_organograma
                          , tipo
                   ) AS organograma_nivel_max
                ON organograma_nivel.cod_organograma = organograma_nivel_max.cod_organograma
               AND organograma_nivel.timestamp       = organograma_nivel_max.timestamp
               AND organograma_nivel.tipo            = organograma_nivel_max.tipo
        ";

        $stOrder = "
          GROUP BY organograma_nivel.cod_organograma
                 , organograma_nivel.cod_nivel
                 , organograma_nivel.tipo
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
