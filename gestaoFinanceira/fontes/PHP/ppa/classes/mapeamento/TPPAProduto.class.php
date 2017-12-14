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
    * Classe de mapeamento da tabela PPA.PRODUTO
    * Data de Criação: 22/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.09.11
*/

/*
$Log: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProduto extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProduto()
    {
        parent::Persistente();

        $this->setTabela('ppa.produto');

        $this->setCampoCod('cod_produto');
        $this->setComplementoChave('');

        $this->AddCampo('cod_produto'  , 'integer', true, ''   , true , false);
        $this->AddCampo('descricao'    , 'varchar', true, '80' , false, false);
        $this->AddCampo('especificacao', 'varchar', true, '450', false, false);
    }

    public function recuperaProdutos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaProdutos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaProdutos()
    {
        $stSql = " SELECT produto.cod_produto
                        , produto.descricao
                        , produto.especificacao
                     FROM ppa.produto ";

        return $stSql;
    }

    /**
     * Verifica se já existe um Produto cadastrado com o nome informado.
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stFiltro
     * @param  string    $stOrder
     * @param  bool      $boTransacao
     * @return RecordSet
     */
    public function recuperaProdutoCadastrado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaProdutoCadastrado",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Monta a string SQL para recuperaProdutoCadastrado
     *
     * @return string SQL
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function montaRecuperaProdutoCadastrado()
    {
        $stSql = "	SELECT UPPER(PP.descricao) as descricao
                         , especificacao
                      FROM ppa.produto PP                                        \n";

        return $stSql;
    }

} // end of class
?>
