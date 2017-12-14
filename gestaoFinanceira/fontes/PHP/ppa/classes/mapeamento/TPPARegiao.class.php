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
    * Classe de mapeamento da tabela PPA.REGIAO
    * Data de Criação   : 22/09/2008

    *
    * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso: uc-02.09.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPARegiao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPARegiao()
    {
        parent::Persistente();

        $this->setTabela('ppa.regiao');

        $this->setCampoCod('cod_regiao');
        $this->setComplementoChave('');

        $this->AddCampo('cod_regiao', 'integer', true, '', true, false);
        $this->AddCampo('nome', 'varchar', true, '80', false, false);
        $this->AddCampo('descricao', 'varchar', false, '240', false, false);
    }

    public function recuperaRegioes(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegioes",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegioes()
    {
        $stSql = "	SELECT regiao.cod_regiao    	        \n";
        $stSql.= "		 , regiao.nome                  	\n";
        $stSql.= "		 , regiao.descricao                	\n";
        $stSql.= "	  FROM ppa.regiao   				    \n";

        return $stSql;
    }

    /**
     * Verifica se já existe uma Região cadastrada com o nome informado.
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stFiltro
     * @param  string    $stOrder
     * @param  bool      $boTransacao
     * @return RecordSet
     */
    public function recuperaRegiaoCadastrada(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegiaoCadastrada",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Monta a string SQL para recuperaRegiaoCadastrada
     *
     * @return string SQL
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function montaRecuperaRegiaoCadastrada()
    {
        $stSql = "	SELECT UPPER(PR.nome) as nome    	\n";
        $stSql.= "	  FROM ppa.regiao PR                                    \n";

        return $stSql;
    }

} // end of class
