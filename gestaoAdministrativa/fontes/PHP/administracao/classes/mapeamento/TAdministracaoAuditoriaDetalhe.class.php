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
* Classe de mapeamento para administracao.auditoria_detalhe
* Data de Criação: 24/09/2012

* @author Analista: Fábio Rodrigues
* @author Desenvolvedor: Matheus Figueredo
*
* $Id: TAdministracaoAuditoriaDetalhe.class.php 64804 2016-04-04 19:29:47Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAuditoriaDetalhe extends Persistente
{
    public function TAuditoriaDetalhe($boTransacao = false)
    {
        //parent::Persistente();   Não pode chamar o construtor da Persistente

        $this->setEstrutura( array() );

        $this->setTabela('administracao.auditoria_detalhe');
        $this->setCampoCod('cod_detalhe');
        $this->setComplementoChave('numcgm, cod_acao, timestamp');

        $this->AddCampo( 'cod_detalhe','integer'  , true );
        $this->AddCampo( 'numcgm'     ,'integer'  , true  ,'' , true , true  );
        $this->AddCampo( 'cod_acao'   ,'integer'  , true  ,'' , true , true  );
        $this->AddCampo( 'timestamp'  ,'timestamp', false ,'' , true , false );
        $this->AddCampo( 'valores'    ,'hstore'   , true  ,'' , false, false, '', array());
    }
}
