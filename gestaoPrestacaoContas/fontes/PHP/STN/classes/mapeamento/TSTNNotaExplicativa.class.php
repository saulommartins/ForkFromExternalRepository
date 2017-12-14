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
 * Mapeamento da tabela stn.nota_explicativa
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 */

require_once CLA_PERSISTENTE;

class TSTNNotaExplicativa extends Persistente
{
    /**
     * Método Construtor da classe TSTNNotaExplicativa
     *
     * @author      Analista        Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela          ('stn.nota_explicativa');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_acao, dt_inicial, dt_final');

        $this->AddCampo('cod_acao'         , 'integer', true, '', true , true );
        $this->AddCampo('dt_inicial'       , 'date'   , true, '', true , false);
        $this->AddCampo('dt_final'         , 'date'   , true, '', true , false);
        $this->AddCampo('nota_explicativa' , 'text'   , true, '', false, false);
    }

    /**
     * Método que retorna os valores dos periodos
     *
     * @author      Analista        Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listNotaExplicativa(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
    {
        $stSql  = "\n SELECT nota_explicativa.cod_acao";
        $stSql .= "\n      , nota_explicativa.dt_inicial";
        $stSql .= "\n      , nota_explicativa.dt_final";
        $stSql .= "\n      , nota_explicativa.nota_explicativa";
        $stSql .= "\n      , acao.nom_acao";
        $stSql .= "\n      , funcionalidade.nom_funcionalidade";
        $stSql .= "\n       FROM stn.nota_explicativa";
        $stSql .= "\n INNER JOIN administracao.acao";
        $stSql .= "\n     ON acao.cod_acao = nota_explicativa.cod_acao";
        $stSql .= "\n INNER JOIN administracao.funcionalidade";
        $stSql .= "\n     ON funcionalidade.cod_funcionalidade = acao.cod_funcionalidade";

        return $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

}
