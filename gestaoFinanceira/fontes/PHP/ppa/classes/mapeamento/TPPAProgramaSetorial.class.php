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
 * Mapeamento da tabela ppa.programa_setorial
 *
 * @category    Urbem
 * @package     PPA
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TPPAProgramaSetorial extends Persistente
{
    /**
     * Método Construtor da classe TPPAProgramaSetorial
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('ppa.programa_setorial');
        $this->setCampoCod        ('cod_setorial');

        $this->AddCampo('cod_setorial' ,'integer'  , true, ''   , true,  true);
        $this->AddCampo('cod_macro'    ,'integer'  , true, ''   , false, TPPAMacroObjetivo);
        $this->AddCampo('descricao'    ,'varchar'  , true, '450', false, false);
        //$this->AddCampo('timestamp'    ,'timestamp', true, ''   , false, false);
    }

    /**
     * Método que retorna os dados completos para a listagem de programa setoriais
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
    public function listProgramaSetorial(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT programa_setorial.cod_setorial
                 , programa_setorial.descricao AS nom_setorial
                 , macro_objetivo.cod_macro
                 , macro_objetivo.descricao AS nom_macro
                 , ppa.cod_ppa
                 , ppa.ano_inicio
                 , ppa.ano_final
              FROM ppa.programa_setorial
        INNER JOIN ppa.macro_objetivo
                ON programa_setorial.cod_macro = macro_objetivo.cod_macro
        INNER JOIN ppa.ppa
                ON macro_objetivo.cod_ppa = ppa.cod_ppa
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
