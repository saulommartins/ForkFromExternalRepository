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
 * Classe Mapeameto do 02.10.03 - Manter Ação
 * Data de Criação: 05/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.silva>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

class TLDOAcaoDados extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.acao_dados');

        $this->setCampoCod('cod_acao_dados');
        $this->setComplementoChave('cod_acao');

        $this->addCampo('cod_acao', 'integer', true, '', true, true);
        $this->addCampo('cod_acao_dados', 'integer', true, '', true, false);
        $this->addCampo('num_orgao', 'integer', true, '', false, true);
        $this->addCampo('num_unidade', 'integer', true, '', false, true);
        $this->addCampo('exercicio', 'character', true, '4', false, true);
        $this->addCampo('cod_entidade', 'integer', true, '', false, true);
        $this->addCampo('cod_norma', 'integer', true, '', false, true);
    }

}
