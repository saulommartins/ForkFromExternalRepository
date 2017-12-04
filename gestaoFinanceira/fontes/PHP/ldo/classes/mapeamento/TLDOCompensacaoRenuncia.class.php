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
 * Classe Mapeameto do UC 02.10.16 - Manter Riscos Fiscais
 * Data de Criação: 23/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.16 - Manter Compensação da Renúncia de Receita
 */

class TLDOCompensacaoRenuncia extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ldo.compensacao_renuncia');
        $this->setCampoCod('cod_compensacao');
        $this->setComplementoChave('ano, cod_ppa');

        // campo, tipo, not_null, data_length, pk, fk
        $this->addCampo('cod_compensacao'  , 'integer', true , ''    , true , false);
        $this->addCampo('ano'              , 'char'   , true , '1'   , true , true);
        $this->addCampo('cod_ppa'          , 'char'   , true , '4'   , true , true);
        $this->addCampo('tributo'          , 'varchar', true , '250' , false, false);
        $this->addCampo('modalidade'       , 'varchar', true , '250' , false, false);
        $this->addCampo('setores_programas', 'varchar', true , '250' , false, false);
        $this->AddCampo('valor_ano_ldo'    , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_ano_ldo_1'  , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_ano_ldo_2'  , 'numeric', false, '14,2', false, false);
        $this->addCampo('compensacao'      , 'varchar', true , '250' , false, false);
    }

}
