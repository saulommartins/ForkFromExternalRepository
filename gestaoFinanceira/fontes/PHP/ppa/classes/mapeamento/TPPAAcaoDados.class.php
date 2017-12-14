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
 * Data de Criação: 03/10/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-02.09.04
 */

class TPPAAcaoDados extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_dados');

        $this->setCampoCod('cod_acao');
        $this->setComplementoChave('timestamp_acao_dados');

        $this->addCampo('cod_acao'            , 'integer'  , true, ''    , true , true);
        $this->addCampo('timestamp_acao_dados', 'timestamp', true, ''    , true , false);
        $this->addCampo('cod_tipo'            , 'integer'  , true, ''    , false, true);
        $this->addCampo('cod_produto'         , 'integer'  , false, ''    , false, true);
        $this->addCampo('cod_regiao'          , 'integer'  , true, ''    , false, true);
        $this->addCampo('exercicio'           , 'character', true, '4'   , false, true);
        $this->addCampo('cod_funcao'          , 'integer'  , false, ''    , false, true);
        $this->addCampo('cod_subfuncao'       , 'integer'  , false, ''    , false, true);
        $this->addCampo('cod_grandeza'        , 'integer'  , true, ''    , false, true);
        $this->addCampo('cod_unidade_medida'  , 'integer'  , false, ''    , false, true);
        $this->addCampo('titulo'              , 'varchar'  , true, '480' , false, false);
        $this->addCampo('descricao'           , 'varchar'  , true, '480' , false, false);
        $this->addCampo('finalidade'          , 'varchar'  , true, '480' , false, false);
        $this->addCampo('cod_forma'           , 'integer'  , true, ''    , false, false);
        $this->addCampo('cod_tipo_orcamento'  , 'integer'  , true, ''    , false, false);
        $this->addCampo('detalhamento'        , 'varchar'  , true, '480' , false, false);
        $this->addCampo('cod_natureza'        , 'integer'  , false, ''    , false, false);
        $this->addCampo('valor_estimado'      , 'numeric'  , true, '14,2', false, false);
        $this->addCampo('meta_estimada'       , 'numeric'  , true, '7,2' , false, false);
    }
}

?>
