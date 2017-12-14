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
 * Classe de mapeamento para Penalidade Baixa Processo
 * Data de Criação: 21/05/2009

 * @author Fernando Piccini Cercato

 * @package URBEM
 * @subpackage Mapeamento

 $Id: $

 * Casos de uso: uc-05.07.05
 */

/**
 * Classe de mapeamento para PenalidadeBaixaProcesso
 */
class TFISPenalidadeBaixaProcesso extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.penalidade_baixa_processo' );

        $this->setCampoCod( '' );
        $this->setComplementoChave( 'cod_penalidade, timestamp_inicio' );

        $this->addCampo( 'cod_penalidade', 'integer', true, '', true, true );
        $this->addCampo( 'timestamp_inicio', 'timestamp', true, '', true, true );
        $this->addCampo( 'cod_processo', 'integer', true, '', false, true );
        $this->addCampo( 'ano_exercicio', 'character', true, '4', false, true );
    }
}
