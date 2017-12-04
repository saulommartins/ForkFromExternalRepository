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
    * Classe de mapeamento da tabela TESOURARIA.RECIBO_EXTRA_TRANSFERENCIA
    * Data de Criação: 06/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.04.26, uc-02.04.27
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaReciboExtraTransferencia extends Persistente
{
   public function TTesourariaReciboExtraTransferencia()
   {
        parent::Persistente();

        $this->setTabela( 'tesouraria.recibo_extra_transferencia' );
        $this->setCampoCod ( '' );
        $this->setComplementoChave ( 'cod_recibo_extra, exercicio, cod_entidade, tipo_recibo' );
        $this->addCampo('cod_entidade'     , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('exercicio'        , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('cod_recibo_extra' , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('tipo_recibo'      , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('cod_lote'         , 'integer'  ,true  , ''  , true, TTesourariaTransferencia );
        $this->AddCampo('tipo'             , 'char'     ,true  , '01', true, TTesourariaTransferencia );
   }
}
