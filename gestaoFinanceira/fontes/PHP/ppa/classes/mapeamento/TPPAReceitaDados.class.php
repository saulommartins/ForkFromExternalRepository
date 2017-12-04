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
 * Classe de mapeamento da tabela ppa.acao_dados
 * Data de Criação: 28/11/2008

 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 * @author Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: TPPAReceitaDados.class.php 36156 2008-12-01 19:49:49Z pedro.medeiros $

 * Casos de uso: uc-02.09.05
 */
class TPPAReceitaDados extends Persistente
{
    /**
     * Método construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ppa.ppa_receita_dados');
        $this->setCampoCod('cod_receita_dados');
        $this->setComplementoChave('cod_ppa, exercicio, cod_conta, cod_entidade');
        // campo, tipo, not_null, data_length, pk, fk
        $this->addCampo('cod_receita',       'integer',   true,  '',  true,  true);
        $this->addCampo('cod_ppa',           'integer',   true,  '',  true,  true );
        $this->addCampo('exercicio',         'char',      true,  '4', true,  true );
        $this->addCampo('cod_conta',         'integer',   true,  '',  true,  true );
        $this->addCampo('cod_entidade',      'integer',   true,  '',  true,  true );
        $this->addCampo('cod_receita_dados', 'integer',   true,  '',  true,  false );
        $this->addCampo('cod_norma',         'integer',   false, '',  false, false );
    }

    /**
     *
     * @param  RecordSet $obRSReceitaDadosNorma
     * @param  string    $stFiltro
     * @param  bool      $boTransacao
     * @return RecordSet
     * @ignore Criado para o ticket #14131
     */
    public function recuperaReceitaDadosNorma($obRSReceitaDadosNorma, $stFiltro, $boTransacao)
    {
        $stOrder = '';

        return $this->executaRecupera("montaRecuperaReceitaDadosNorma", $obRSReceitaDadosNorma, $stFiltro, $stOrder, $boTransacao);
    }

    /**
     *
     * @return string SQL
     * @ignore Criado para o ticket #14131
     */
    protected function montaRecuperaReceitaDadosNorma()
    {
        $stSql  = "    SELECT PPARD.cod_norma                            \n";
        $stSql .= "      FROM ppa.ppa_receita_dados PPARD                \n";

        return $stSql;
    }

}
?>
