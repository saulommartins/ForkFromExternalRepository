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
    * Data de Criação: 29/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: TFrotaEfetivacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaEfetivacao extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaEfetivacao()
    {
        parent::Persistente();
        $this->setTabela('frota.efetivacao');
        $this->setCampoCod('cod_manutencao');
        $this->setComplementoChave('cod_autorizacao,exercicio_manutencao,exercicio_autorizacao');
        $this->AddCampo('cod_manutencao','integer',true,'',true,true);
        $this->AddCampo('exercicio_manutencao','varchar',true,'"4"',true,true);
        $this->AddCampo('cod_autorizacao','integer',true,'',true,true);
        $this->AddCampo('exercicio_autorizacao','varchar',true,'"4"',true,true);
    }

}
