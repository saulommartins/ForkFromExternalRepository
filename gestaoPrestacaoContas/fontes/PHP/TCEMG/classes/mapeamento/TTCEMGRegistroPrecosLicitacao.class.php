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
 * Classe de mapeamento da tabela tcemg.registro_precos_licitacao
 * Data de Criação: 24/06/2015
 * 
 * @author Analista      : Gelson Wolowski Gonçalves
 * @author Desenvolvedor : Michel Teixeira
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGRegistroPrecosLicitacao.class.php 62832 2015-06-25 16:55:06Z michel $
 * 
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGRegistroPrecosLicitacao extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function TTCEMGRegistroPrecosLicitacao()
    {
        parent::Persistente();

        $this->setTabela('tcemg.registro_precos_licitacao');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador, cod_licitacao, cod_modalidade, cod_entidade_licitacao, exercicio_licitacao');

        $this->addCampo('cod_entidade'          , 'integer', true, '' , true, true );
        $this->addCampo('numero_registro_precos', 'integer', true, '' , true, true );
        $this->addCampo('exercicio'             , 'varchar', true, '4', true, true );
        $this->addCampo('interno'               , 'boolean', true, '' , true, true );
        $this->addCampo('numcgm_gerenciador'    , 'integer', true, '' , true, true );
        $this->addCampo('cod_licitacao'         , 'integer', true, '' , true, true );
        $this->addCampo('cod_modalidade'        , 'integer', true, '' , true, true );
        $this->addCampo('cod_entidade_licitacao', 'integer', true, '' , true, true );
        $this->addCampo('exercicio_licitacao'   , 'varchar', true, '4', true, true );        
    }
    
    public function __destruct(){}

}

?>