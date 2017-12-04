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
 * Componente que monta os combos de banco, agencia e um buscainner para a conta
 *
 * @category    Urbem
 * @package     Monetario
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: IMontaAgenciaConta.class.php 59612 2014-09-02 12:00:51Z gelson $
 */

include CAM_GT_MON_COMPONENTES . 'IMontaAgencia.class.php';

class IMontaAgenciaConta extends Objeto
{
    public $obIMontaAgencia,
        $obBscConta,
        $boVinculoPlanoBanco,
        $inCodEntidadeVinculo;

    /**
     * setVinculoPlanoBanco
     * Seta o valor a propriedade $boVinculoPlanoBanco que serve para identificar se é necessário ou não realizar o vinculo com a tabela
     * contabiliadade.plano_banco
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @param  boolean $boValue
     * @return void
     */
    public function setVinculoPlanoBanco($boValue)
    {
        $this->boVinculoPlanoBanco = $boValue;
    }

    /**
     * setCodEntidadeVinculo
     * Seta o valor a propriedade $inCodEntidadeVinculo que serve para filtrar a entidade necessária no vinculo com a tabela
     * contabiliadade.plano_banco. O valor setado aqui só será usado caso a propriedade $boVinculoPlanoBanco esteja setada como true
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @param  Integer $inValue
     * @return void
     */
    public function setCodEntidadeVinculo($inValue)
    {
        $this->inCodEntidadeVinculo = $inValue;
    }

    /**
     * getVinculoPlanoBanco
     * Retorna o valor a propriedade $boVinculoPlanoBanco
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @return boolean
     */
    public function getVinculoPlanoBanco()
    {
        return $this->boVinculoPlanoBanco;
    }

    /**
     * getCodEntidadeVinculo
     * Retorna o valor a propriedade $inCodEntidadeVinculo
     *
     * @author Analista      Tonismar Bernardo           <tonismar.bernardo@cnm.org.br>
     * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     * @return Integer
     */
    public function getCodEntidadeVinculo()
    {
        return $this->inCodEntidadeVinculo;
    }

    /**
     * Metodo construtor da classe IMontaAgenciaConta
     *
     * Instancia o componente IMontaAgencia e o configura um componente
     * buscaInner para a conta
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        //Instancia o componente IMontaAgencia
        $this->obIMontaAgencia = new IMontaAgencia();
        $this->obIMontaAgencia->obTextBoxSelectAgencia->setNull(false);
        $this->obIMontaAgencia->obITextBoxSelectBanco->setNull (false);

        //Instancia o componente buscaInner para a conta
        $this->obBscConta = new BuscaInner();
        $this->obBscConta->setRotulo               ('Conta Corrente'                    );
        $this->obBscConta->setTitle                ('Digite o número da conta corrente.');
        $this->obBscConta->setNulL                 (false                               );
        $this->obBscConta->obCampoCod->setName     ('stContaCorrente'                   );
        $this->obBscConta->obCampoCod->setId       ('stContaCorrente'                   );
        $this->obBscConta->obCampoCod->setSize     (20                                  );
        $this->obBscConta->obCampoCod->setMaxLength(20                                  );
        $this->obBscConta->obCampoCod->setAlign    ('left'                              );
        $this->obBscConta->obCampoCod->setInteiro  (false                               );
        $this->setVinculoPlanoBanco                (false                               );
    }

    /**
     * Metodo que adiciona os componentes criados no formulario
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return object Formulario
     */
    public function geraFormulario(&$obFormulario)
    {   //Dados que serao passados por parametro
        $stParamsPopUp = "?inCodBanco='+jq('#inCodBancoTxt').val()+'&stNumAgencia='+jq('#stNumAgenciaTxt').val()+'&paginando=0&";
        if ($this->boVinculoPlanoBanco) {
            $this->obIMontaAgencia->boVinculoPlanoBanco = true;
            $stParamsPopUp .= "boVinculoPlanoBanco=true&";
            $inCodEntidade = $this->getCodEntidadeVinculo();
            if ($inCodEntidade != '') {
                $stParamsPopUp .= "inCodEntidadeVinculo=".$inCodEntidade."&";
            }
        }
        //Condicao para que a popup seja utilizada. O banco e a agencia tem que estar setados
        $stCondicaoPopUp  = "ajaxJavaScript('".CAM_GT_MON_INSTANCIAS."processamento/OCIMontaAgenciaConta.php?','limpaFiltro');";
        $stCondicaoPopUp .= "if (jq('#stNumAgencia').val() == '') { alertaAviso('Informe o Banco e a Agência','frm','erro','".Sessao::getId()."'); }";
        $stCondicaoPopUp .= "else{ abrePopUp('".CAM_GT_MON_POPUPS."contaCorrente/LSProcurarConta.php".$stParamsPopUp."','frm','stContaCorrente','','','".Sessao::getId()."','800','550'); }";

        $stParamsBlur  = $stParamsPopUp . "&stNumeroConta='+jq('#stContaCorrente').val()+'";

        $this->obBscConta->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".CAM_GT_MON_INSTANCIAS."processamento/OCIMontaAgenciaConta.php".$stParamsBlur."','buscaConta');");
        $this->obBscConta->setFuncaoBusca                 ($stCondicaoPopUp);

        $this->obIMontaAgencia->geraFormulario($obFormulario    );
        $obFormulario->addComponente          ($this->obBscConta);
    }
}
?>
