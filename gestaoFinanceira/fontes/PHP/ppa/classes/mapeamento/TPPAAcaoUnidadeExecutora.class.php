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
 * Classe de mapeamento da tabela ppa.acao_unidade_executora

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: $

 * Casos de uso: uc-02.09.04
 */

class TPPAAcaoUnidadeExecutora extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_unidade_executora');

        $this->setCampoCod('cod_acao');
        $this->setComplementoChave('timestamp_acao_dados, exercicio_unidade, num_unidade, num_orgao');

        $this->addCampo('cod_acao', 'integer', true, '', true, false);
        $this->addCampo('timestamp_acao_dados', 'timestamp', true, '', false, true);
        $this->addCampo('exercicio_unidade', 'vachar', true, '4', true, true);
        $this->addCampo('num_unidade', 'integer', true, '', true, true);
        $this->addCampo('num_orgao', 'integer', true, '', true, true);
    }

    public function recuperaUnidadeExecutora(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT acao_unidade_executora.num_unidade
                 , acao_unidade_executora.num_orgao
                 , unidade.nom_unidade
              FROM ppa.acao_unidade_executora
        INNER JOIN orcamento.unidade
                ON acao_unidade_executora.exercicio_unidade    = unidade.exercicio
               AND acao_unidade_executora.num_unidade          = unidade.num_unidade
               AND acao_unidade_executora.num_orgao            = unidade.num_orgao
             WHERE acao_unidade_executora.cod_acao             = ".$this->getDado('cod_acao')."
               AND acao_unidade_executora.timestamp_acao_dados = '".$this->getDado('timestamp_acao_dados')."'
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
