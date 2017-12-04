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
 * Classe de mapeamento para notificacao_termo_infracao
 * Entidade de associação entre:
 * notificacao_termo (cod_processo, num_notificacao) -> infracao (cod_infracao)
 * Data de Criação: 21/11/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Marcio Medeiros <marcio.medeiros@cnm.org.br>

 * @package URBEM
 * @subpackage Mapeamento

 * $Id: TFISNotificacaoTermoInfracao.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */
class TFISNotificacaoTermoInfracao extends Persistente
{

    /**
     * Método construtor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela( 'fiscalizacao.notificacao_termo_infracao' );
        $this->setCampoCod( 'cod_processo' );
        $this->setComplementoChave( 'num_notificacao', 'cod_infracao' );
        // campo, tipo, not_null, data_length, pk, fk
        $this->addCampo( 'cod_processo', 'integer', true, '', true, true );
        $this->addCampo( 'cod_infracao', 'integer', true, '', false, false );
        $this->addCampo( 'num_notificacao', 'integer', true, '', true, true );
        $this->addCampo( 'observacao', 'text', false, '', false, false );
    }

}
?>
