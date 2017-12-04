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
    * @author Analista: Gelson Goncalves
    * @author Desenvolvedor: Lisiane Morais
    * $Id: TLicitacaoJustificativaRazao.class.php 63178 2015-07-31 20:11:32Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoJustificativaRazao extends Persistente
{
    public function TLicitacaoJustificativaRazao()
    {
        parent::Persistente();
        $this->setTabela("licitacao.justificativa_razao");

        $this->setCampoCod('cod_licitacao');
        $this->setComplementoChave('cod_entidade,exercicio,cod_modalidade');

        $this->AddCampo( 'cod_licitacao'	   ,'integer' , true  , ''	  , true  , false );
        $this->AddCampo( 'cod_entidade'        ,'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'cod_modalidade'      ,'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'exercicio'           ,'char'	  , true  , '4'   , true  , true  );
        $this->AddCampo( 'justificativa'       ,'varchar' , false , '250' , false , false );
        $this->AddCampo( 'razao'               ,'varchar' , false , '250' , false , false );
        $this->AddCampo( 'fundamentacao_legal' ,'varchar' , false , '250' , false , false );
    }
}
