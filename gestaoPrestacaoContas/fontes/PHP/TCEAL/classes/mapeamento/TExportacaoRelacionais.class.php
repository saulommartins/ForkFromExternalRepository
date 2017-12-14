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
    *
    * Mapeamento da
    * Data de Criação   : 03/10/2013
    *
    * @author Desenvolvedor: Carolina Schwaab Marçal
    *
    * @package URBEM
    * @subpackage Configuração
    * $Id:  $
    *
    * @ignore
*/

include_once CLA_PERSISTENTE;

class TExportacaoRelacionais extends Persistente
{
    /**
     * Método Construtor da classe TExportacaoRelacionais
     *
     * @author      Desenvolvedor   Carloina Schwaab Marçal
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
     * Método que retorna dados para informar no xml da exportação
     *
     * @author      Desenvolvedor   Carolina Schwaab Marçal
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function buscaDatas(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
    {
        $stSql =" SELECT * FROM publico.bimestre('".$this->getDado('stExercicio')."', ".$this->getDado('inBimestre')." )";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

}
