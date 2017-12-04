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
 * Classe de controle - STN - Configuracao
 *
 * @category    Urbem
 * @package     STN
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: CSTNConfiguracao.class.php 66478 2016-09-01 17:09:26Z lisiane $
 */

include CAM_FW_COMPONENTES . 'Table/TableTree.class.php';

class CSTNConfiguracao
{
    public $obModel
          ,$arMes;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
        //Monta um array com todos os meses
        $this->arMes = array( '1'  => 'Janeiro'
                             ,'2'  => 'Fevereiro'
                             ,'3'  => 'Março'
                             ,'4'  => 'Abril'
                             ,'5'  => 'Maio'
                             ,'6'  => 'Junho'
                             ,'7'  => 'Julho'
                             ,'8'  => 'Agosto'
                             ,'9'  => 'Setembro'
                             ,'10' => 'Outubro'
                             ,'11' => 'Novembro'
                             ,'12' => 'Dezembro');
    }

    /**
     * Metodo montaFormulario, monta o formulario de vinculo de receita corrente liquida
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array  $arParam Array de dados
     * @param string $stTitle String com o titulo para o formulario
     *
     * @return void
     */
    public function montaFormRCL($arParam)
    {
        if ($arParam['stDataImplantacao'] != '') {
            //Inclui o componente ITextBoxSelectEntidadeGeral
            include CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';

            $arData = explode('/',$arParam['stDataImplantacao']);

            //Instancia o componente ITextBoxSelectEntidadeGeral
            $obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
            $obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->inExercicio = $arData[2];
            $obITextBoxSelectEntidadeGeral->setObrigatorioBarra(true);

            //Preenche o array do periodo com os 12 meses anteriores a data de imp
            for ($i=($arData[1] - 1); $i > ($arData[1] - 14); $i--) {
                if ($i != '0') {
                    $inMes = $i;
                    $inAno = $arData[2];
                    if ($i < 0) {
                        $inMes = 13 + $i;
                        $inAno--;
                    }
                    $arPeriodo[(abs($inMes) . '/' . $inAno)] = ($this->arMes[abs($inMes)] . '/' . $inAno);
                }
            }

            //Instancia um select
            $obSlPeriodo = new Select();
            $obSlPeriodo->setName    ('stPeriodo');
            $obSlPeriodo->setId      ('stPeriodo');
            $obSlPeriodo->setRotulo  ('Período');
            $obSlPeriodo->setTitle   ('Informe o período.');
            $obSlPeriodo->obEvento->setOnChange("montaParametrosGET('habilitaReceitaCorrente');");
            $obSlPeriodo->addOption  ('', 'Selecione');
            foreach ($arPeriodo as $stKey => $stValue) {
                $obSlPeriodo->addOption($stKey, $stValue);
            }
            $obSlPeriodo->setObrigatorioBarra(true);

            $obLblReceitaCorrente = new Label();
            $obLblReceitaCorrente->setId('lblReceitaCorrente');
            $obLblReceitaCorrente->setRotulo('RECEITAS CORRENTES');

            //Valor Receita Tributaria
            $obNuValorReceitaTributaria = new Numerico();
            $obNuValorReceitaTributaria->setName            ('nuValorReceitaTributaria');
            $obNuValorReceitaTributaria->setId              ('nuValorReceitaTributaria');
            $obNuValorReceitaTributaria->setRotulo          ('Receita Tributaria');
            $obNuValorReceitaTributaria->setTitle           ('Receita Tributaria');
            $obNuValorReceitaTributaria->setSize            (20);
            $obNuValorReceitaTributaria->setNegativo        (true);
            $obNuValorReceitaTributaria->setValue           ('0,00');
            $obNuValorReceitaTributaria->setObrigatorioBarra(true);
            $obNuValorReceitaTributaria->setMaxLength       (17);
            $obNuValorReceitaTributaria->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor IPTU
            $obNuValorIptu = new Numerico();
            $obNuValorIptu->setName            ('nuValorIptu');
            $obNuValorIptu->setId              ('nuValorIptu');
            $obNuValorIptu->setRotulo          ('IPTU');
            $obNuValorIptu->setTitle           ('IPTU');
            $obNuValorIptu->setSize            (20);
            $obNuValorIptu->setNegativo        (true);
            $obNuValorIptu->setValue           ('0,00');
            $obNuValorIptu->setObrigatorioBarra(true);
            $obNuValorIptu->setMaxLength       (17);
            $obNuValorIptu->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor ISS
            $obNuValorIss = new Numerico();
            $obNuValorIss->setName            ('nuValorIss');
            $obNuValorIss->setId              ('nuValorIss');
            $obNuValorIss->setRotulo          ('ISS');
            $obNuValorIss->setTitle           ('ISS');
            $obNuValorIss->setSize            (20);
            $obNuValorIss->setNegativo        (true);
            $obNuValorIss->setValue           ('0,00');
            $obNuValorIss->setObrigatorioBarra(true);
            $obNuValorIss->setMaxLength       (17);
            $obNuValorIss->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor ITBI
            $obNuValorItbi = new Numerico();
            $obNuValorItbi->setName            ('nuValorItbi');
            $obNuValorItbi->setId              ('nuValorItbi');
            $obNuValorItbi->setRotulo          ('ITBI');
            $obNuValorItbi->setTitle           ('ITBI');
            $obNuValorItbi->setSize            (20);
            $obNuValorItbi->setNegativo        (true);
            $obNuValorItbi->setValue           ('0,00');
            $obNuValorItbi->setObrigatorioBarra(true);
            $obNuValorItbi->setMaxLength       (17);
            $obNuValorItbi->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor IRRF
            $obNuValorIrrf = new Numerico();
            $obNuValorIrrf->setName            ('nuValorIrrf');
            $obNuValorIrrf->setId              ('nuValorIrrf');
            $obNuValorIrrf->setRotulo          ('IRRF');
            $obNuValorIrrf->setTitle           ('IRRF');
            $obNuValorIrrf->setSize            (20);
            $obNuValorIrrf->setNegativo        (true);
            $obNuValorIrrf->setValue           ('0,00');
            $obNuValorIrrf->setObrigatorioBarra(true);
            $obNuValorIrrf->setMaxLength       (17);
            $obNuValorIrrf->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
             //Valor IRRF
            $obNuValorIrrf = new Numerico();
            $obNuValorIrrf->setName            ('nuValorIrrf');
            $obNuValorIrrf->setId              ('nuValorIrrf');
            $obNuValorIrrf->setRotulo          ('IRRF');
            $obNuValorIrrf->setTitle           ('IRRF');
            $obNuValorIrrf->setSize            (20);
            $obNuValorIrrf->setNegativo        (true);
            $obNuValorIrrf->setValue           ('0,00');
            $obNuValorIrrf->setObrigatorioBarra(true);
            $obNuValorIrrf->setMaxLength       (17);
            $obNuValorIrrf->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Outras Receitas Tributárias
            $obNuValorOutrasReceitasTributarias = new Numerico();
            $obNuValorOutrasReceitasTributarias->setName            ('nuValorOutrasReceitasTributarias');
            $obNuValorOutrasReceitasTributarias->setId              ('nuValorOutrasReceitasTributarias');
            $obNuValorOutrasReceitasTributarias->setRotulo          ('Outras Receitas Tributárias');
            $obNuValorOutrasReceitasTributarias->setTitle           ('Outras Receitas Tributárias');
            $obNuValorOutrasReceitasTributarias->setSize            (20);
            $obNuValorOutrasReceitasTributarias->setNegativo        (true);
            $obNuValorOutrasReceitasTributarias->setValue           ('0,00');
            $obNuValorOutrasReceitasTributarias->setObrigatorioBarra(true);
            $obNuValorOutrasReceitasTributarias->setMaxLength       (17);
            $obNuValorOutrasReceitasTributarias->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Receita de Contribuições
            $obNuValorReceitaContribuicoes = new Numerico();
            $obNuValorReceitaContribuicoes->setName            ('nuValorReceitaContribuicoes');
            $obNuValorReceitaContribuicoes->setId              ('nuValorReceitaContribuicoes');
            $obNuValorReceitaContribuicoes->setRotulo          ('Receita de Contribuições');
            $obNuValorReceitaContribuicoes->setTitle           ('Receita de Contribuições');
            $obNuValorReceitaContribuicoes->setSize            (20);
            $obNuValorReceitaContribuicoes->setNegativo        (true);
            $obNuValorReceitaContribuicoes->setValue           ('0,00');
            $obNuValorReceitaContribuicoes->setObrigatorioBarra(true);
            $obNuValorReceitaContribuicoes->setMaxLength       (17);
            $obNuValorReceitaContribuicoes->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Receita Patrimonial
            $obNuValorReceitaPatrominial = new Numerico();
            $obNuValorReceitaPatrominial->setName            ('nuValorReceitaPatrominial');
            $obNuValorReceitaPatrominial->setId              ('nuValorReceitaPatrominial');
            $obNuValorReceitaPatrominial->setRotulo          ('Receita Patrimonial');
            $obNuValorReceitaPatrominial->setTitle           ('Receita Patrimonial');
            $obNuValorReceitaPatrominial->setSize            (20);
            $obNuValorReceitaPatrominial->setNegativo        (true);
            $obNuValorReceitaPatrominial->setValue           ('0,00');
            $obNuValorReceitaPatrominial->setObrigatorioBarra(true);
            $obNuValorReceitaPatrominial->setMaxLength       (17);
            $obNuValorReceitaPatrominial->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Receita Agropecuária
            $obNuValorReceitaAgropecuaria = new Numerico();
            $obNuValorReceitaAgropecuaria->setName            ('nuValorReceitaAgropecuaria');
            $obNuValorReceitaAgropecuaria->setId              ('nuValorReceitaAgropecuaria');
            $obNuValorReceitaAgropecuaria->setRotulo          ('Receita Agropecuária');
            $obNuValorReceitaAgropecuaria->setTitle           ('Receita Agropecuária');
            $obNuValorReceitaAgropecuaria->setSize            (20);
            $obNuValorReceitaAgropecuaria->setNegativo        (true);
            $obNuValorReceitaAgropecuaria->setValue           ('0,00');
            $obNuValorReceitaAgropecuaria->setObrigatorioBarra(true);
            $obNuValorReceitaAgropecuaria->setMaxLength       (17);
            $obNuValorReceitaAgropecuaria->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Receita Industrial
            $obNuValorReceitaIndustrial = new Numerico();
            $obNuValorReceitaIndustrial->setName            ('nuValorReceitaIndustrial');
            $obNuValorReceitaIndustrial->setId              ('nuValorReceitaIndustrial');
            $obNuValorReceitaIndustrial->setRotulo          ('Receita Industrial');
            $obNuValorReceitaIndustrial->setTitle           ('Receita Industrial');
            $obNuValorReceitaIndustrial->setSize            (20);
            $obNuValorReceitaIndustrial->setNegativo        (true);
            $obNuValorReceitaIndustrial->setValue           ('0,00');
            $obNuValorReceitaIndustrial->setObrigatorioBarra(true);
            $obNuValorReceitaIndustrial->setMaxLength       (17);
            $obNuValorReceitaIndustrial->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Receita de Serviços
            $obNuValorReceitaServicos = new Numerico();
            $obNuValorReceitaServicos->setName            ('nuValorReceitaServicos');
            $obNuValorReceitaServicos->setId              ('nuValorReceitaServicos');
            $obNuValorReceitaServicos->setRotulo          ('Receita de Serviços');
            $obNuValorReceitaServicos->setTitle           ('Receita de Serviços');
            $obNuValorReceitaServicos->setSize            (20);
            $obNuValorReceitaServicos->setNegativo        (true);
            $obNuValorReceitaServicos->setValue           ('0,00');
            $obNuValorReceitaServicos->setObrigatorioBarra(true);
            $obNuValorReceitaServicos->setMaxLength       (17);
            $obNuValorReceitaServicos->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Transferências Correntes
            $obNuValorTransferenciaCorrente = new Numerico();
            $obNuValorTransferenciaCorrente->setName            ('nuValorTransferenciaCorrente');
            $obNuValorTransferenciaCorrente->setId              ('nuValorTransferenciaCorrente');
            $obNuValorTransferenciaCorrente->setRotulo          ('Transferências Correntes');
            $obNuValorTransferenciaCorrente->setTitle           ('Transferências Correntes');
            $obNuValorTransferenciaCorrente->setSize            (20);
            $obNuValorTransferenciaCorrente->setNegativo        (true);
            $obNuValorTransferenciaCorrente->setValue           ('0,00');
            $obNuValorTransferenciaCorrente->setObrigatorioBarra(true);
            $obNuValorTransferenciaCorrente->setMaxLength       (17);
            $obNuValorTransferenciaCorrente->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Cota-Parte do FPM
            $obNuValorCotaParteFPM = new Numerico();
            $obNuValorCotaParteFPM->setName            ('nuValorCotaParteFPM');
            $obNuValorCotaParteFPM->setId              ('nuValorCotaParteFPM');
            $obNuValorCotaParteFPM->setRotulo          ('Cota-Parte do FPM');
            $obNuValorCotaParteFPM->setTitle           ('Cota-Parte do FPM');
            $obNuValorCotaParteFPM->setSize            (20);
            $obNuValorCotaParteFPM->setNegativo        (true);
            $obNuValorCotaParteFPM->setValue           ('0,00');
            $obNuValorCotaParteFPM->setObrigatorioBarra(true);
            $obNuValorCotaParteFPM->setMaxLength       (17);
            $obNuValorCotaParteFPM->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Cota-Parte do ICMS
            $obNuValorCotaParteICMS = new Numerico();
            $obNuValorCotaParteICMS->setName            ('nuValorCotaParteICMS');
            $obNuValorCotaParteICMS->setId              ('nuValorCotaParteICMS');
            $obNuValorCotaParteICMS->setRotulo          ('Cota-Parte do ICMS');
            $obNuValorCotaParteICMS->setTitle           ('Cota-Parte do ICMS');
            $obNuValorCotaParteICMS->setSize            (20);
            $obNuValorCotaParteICMS->setNegativo        (true);
            $obNuValorCotaParteICMS->setValue           ('0,00');
            $obNuValorCotaParteICMS->setObrigatorioBarra(true);
            $obNuValorCotaParteICMS->setMaxLength       (17);
            $obNuValorCotaParteICMS->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Cota-Parte do IPVA
            $obNuValorCotaParteIPVA = new Numerico();
            $obNuValorCotaParteIPVA->setName            ('nuValorCotaParteIPVA');
            $obNuValorCotaParteIPVA->setId              ('nuValorCotaParteIPVA');
            $obNuValorCotaParteIPVA->setRotulo          ('Cota-Parte do IPVA');
            $obNuValorCotaParteIPVA->setTitle           ('Cota-Parte do IPVA');
            $obNuValorCotaParteIPVA->setSize            (20);
            $obNuValorCotaParteIPVA->setNegativo        (true);
            $obNuValorCotaParteIPVA->setValue           ('0,00');
            $obNuValorCotaParteIPVA->setObrigatorioBarra(true);
            $obNuValorCotaParteIPVA->setMaxLength       (17);
            $obNuValorCotaParteIPVA->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Cota-Parte do ITR
            $obNuValorCotaParteITR = new Numerico();
            $obNuValorCotaParteITR->setName            ('nuValorCotaParteITR');
            $obNuValorCotaParteITR->setId              ('nuValorCotaParteITR');
            $obNuValorCotaParteITR->setRotulo          ('Cota-Parte do ITR');
            $obNuValorCotaParteITR->setTitle           ('Cota-Parte do ITR');
            $obNuValorCotaParteITR->setSize            (20);
            $obNuValorCotaParteITR->setNegativo        (true);
            $obNuValorCotaParteITR->setValue           ('0,00');
            $obNuValorCotaParteITR->setObrigatorioBarra(true);
            $obNuValorCotaParteITR->setMaxLength       (17);
            $obNuValorCotaParteITR->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Transferências da LC 87/1996
            $obNuValorTransferenciaLC871996 = new Numerico();
            $obNuValorTransferenciaLC871996->setName            ('nuValorTransferenciaLC871996');
            $obNuValorTransferenciaLC871996->setId              ('nuValorTransferenciaLC871996');
            $obNuValorTransferenciaLC871996->setRotulo          ('Transferências da LC 87/1996');
            $obNuValorTransferenciaLC871996->setTitle           ('Transferências da LC 87/1996');
            $obNuValorTransferenciaLC871996->setSize            (20);
            $obNuValorTransferenciaLC871996->setNegativo        (true);
            $obNuValorTransferenciaLC871996->setValue           ('0,00');
            $obNuValorTransferenciaLC871996->setObrigatorioBarra(true);
            $obNuValorTransferenciaLC871996->setMaxLength       (17);
            $obNuValorTransferenciaLC871996->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Transferências da LC 61/1989
            $obNuValorTransferenciaLC611989 = new Numerico();
            $obNuValorTransferenciaLC611989->setName            ('nuValorTransferenciaLC611989');
            $obNuValorTransferenciaLC611989->setId              ('nuValorTransferenciaLC611989');
            $obNuValorTransferenciaLC611989->setRotulo          ('Transferências da LC 61/1989');
            $obNuValorTransferenciaLC611989->setTitle           ('Transferências da LC 61/1989');
            $obNuValorTransferenciaLC611989->setSize            (20);
            $obNuValorTransferenciaLC611989->setNegativo        (true);
            $obNuValorTransferenciaLC611989->setValue           ('0,00');
            $obNuValorTransferenciaLC611989->setObrigatorioBarra(true);
            $obNuValorTransferenciaLC611989->setMaxLength       (17);
            $obNuValorTransferenciaLC611989->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Transferências do FUNDEB 
            $obnuValorTransferenciasFundeb = new Numerico();
            $obnuValorTransferenciasFundeb->setName            ('nuValorTransferenciasFundeb');
            $obnuValorTransferenciasFundeb->setId              ('nuValorTransferenciasFundeb');
            $obnuValorTransferenciasFundeb->setRotulo          ('Transferências do FUNDEB');
            $obnuValorTransferenciasFundeb->setTitle           ('Transferências do FUNDEB');
            $obnuValorTransferenciasFundeb->setSize            (20);
            $obnuValorTransferenciasFundeb->setNegativo        (true);
            $obnuValorTransferenciasFundeb->setValue           ('0,00');
            $obnuValorTransferenciasFundeb->setObrigatorioBarra(true);
            $obnuValorTransferenciasFundeb->setMaxLength       (17);
            $obnuValorTransferenciasFundeb->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Outras Transferências Correntes
            $obnuValorOutrasTransferenciasCorrentes = new Numerico();
            $obnuValorOutrasTransferenciasCorrentes->setName            ('nuValorOutrasTransferenciasCorrentes');
            $obnuValorOutrasTransferenciasCorrentes->setId              ('nuValorOutrasTransferenciasCorrentes');
            $obnuValorOutrasTransferenciasCorrentes->setRotulo          ('Outras Transferências Correntes');
            $obnuValorOutrasTransferenciasCorrentes->setTitle           ('Outras Transferências Correntes');
            $obnuValorOutrasTransferenciasCorrentes->setSize            (20);
            $obnuValorOutrasTransferenciasCorrentes->setNegativo        (true);
            $obnuValorOutrasTransferenciasCorrentes->setValue           ('0,00');
            $obnuValorOutrasTransferenciasCorrentes->setObrigatorioBarra(true);
            $obnuValorOutrasTransferenciasCorrentes->setMaxLength       (17);
            $obnuValorOutrasTransferenciasCorrentes->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");
            
            //Valor Outras Receitas Correntes
            $obnuValorOutrasReceitas = new Numerico();
            $obnuValorOutrasReceitas->setName            ('nuValorOutrasReceitas');
            $obnuValorOutrasReceitas->setId              ('nuValorOutrasReceitas');
            $obnuValorOutrasReceitas->setRotulo          ('Outras Receitas Correntes');
            $obnuValorOutrasReceitas->setTitle           ('Outras Receitas Correntes');
            $obnuValorOutrasReceitas->setSize            (20);
            $obnuValorOutrasReceitas->setNegativo        (true);
            $obnuValorOutrasReceitas->setValue           ('0,00');
            $obnuValorOutrasReceitas->setObrigatorioBarra(true);
            $obnuValorOutrasReceitas->setMaxLength       (17);
            $obnuValorOutrasReceitas->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            $obLblDeducoes = new Label();
            $obLblDeducoes->setId('lblDeducoes');
            $obLblDeducoes->setRotulo('DEDUÇÕES');

            //Valor Contrib. Plano Seg. Social Servidor
            $obNuValorContribPlanoSSS = new Numerico();
            $obNuValorContribPlanoSSS->setName            ('nuValorContribPlanoSSS');
            $obNuValorContribPlanoSSS->setId              ('nuValorContribPlanoSSS');
            $obNuValorContribPlanoSSS->setRotulo          ('Contrib. Plano Seg. Social Servidor');
            $obNuValorContribPlanoSSS->setTitle           ('Contrib. Plano Seg. Social Servidor');
            $obNuValorContribPlanoSSS->setSize            (20);
            $obNuValorContribPlanoSSS->setNegativo        (true);
            $obNuValorContribPlanoSSS->setValue           ('0,00');
            $obNuValorContribPlanoSSS->setObrigatorioBarra(true);
            $obNuValorContribPlanoSSS->setMaxLength       (17);
            $obNuValorContribPlanoSSS->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Compensação Financ. entre Regimes Previd
            $obNuValorCompensacaoFinanceira = new Numerico();
            $obNuValorCompensacaoFinanceira->setName            ('nuValorCompensacaoFinanceira');
            $obNuValorCompensacaoFinanceira->setId              ('nuValorCompensacaoFinanceira');
            $obNuValorCompensacaoFinanceira->setRotulo          ('Compensação Financ. entre Regimes Previd');
            $obNuValorCompensacaoFinanceira->setTitle           ('Compensação Financ. entre Regimes Previd');
            $obNuValorCompensacaoFinanceira->setSize            (20);
            $obNuValorCompensacaoFinanceira->setNegativo        (true);
            $obNuValorCompensacaoFinanceira->setValue           ('0,00');
            $obNuValorCompensacaoFinanceira->setObrigatorioBarra(true);
            $obNuValorCompensacaoFinanceira->setMaxLength       (17);
            $obNuValorCompensacaoFinanceira->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor Dedução Fundeb
            $obNuValorDeducaoFundeb = new Numerico();
            $obNuValorDeducaoFundeb->setName            ('nuValorDeducaoFundeb');
            $obNuValorDeducaoFundeb->setId              ('nuValorDeducaoFundeb');
            $obNuValorDeducaoFundeb->setRotulo          ('Dedução Fundeb');
            $obNuValorDeducaoFundeb->setTitle           ('Dedução Fundeb');
            $obNuValorDeducaoFundeb->setSize            (20);
            $obNuValorDeducaoFundeb->setNegativo        (true);
            $obNuValorDeducaoFundeb->setValue           ('0,00');
            $obNuValorDeducaoFundeb->setObrigatorioBarra(true);
            $obNuValorDeducaoFundeb->setMaxLength       (17);
            $obNuValorDeducaoFundeb->obEvento->setOnChange(" montaParametrosGET('somaValoresRCL',''); ");

            //Valor TOTAL DA RCL ( Receitas Correntes - Deduções )
            $obLblTotalRCL = new Label();
            $obLblTotalRCL->setId('lblTotalRCL');
            $obLblTotalRCL->setRotulo('TOTAL DA RECEITA CORRENTE LÍQUIDA ');

            //Instancia um textbox para o valor do cheque
            $obNumValor = new Numerico();
            $obNumValor->setName            ('flValor');
            $obNumValor->setId              ('flValor');
            $obNumValor->setRotulo          ('Valor');
            $obNumValor->setTitle           ('Valor da Receita Corrente Líquida');
            $obNumValor->setObrigatorioBarra(true);
            $obNumValor->setNegativo        (true);
            $obNumValor->setMaxLength       (17);

            $obFormularioValor = new Formulario();
            $obFormularioValor->setForm(FALSE);
            $obFormularioValor->addComponente  ( $obNumValor );
            $obFormularioValor->montaHTML();

            $obSpanValor= new Span;
            $obSpanValor->setId( 'spnValor' );
            $obSpanValor->setValue( $obFormularioValor->getHTML());

            #Receitas Correntes
            $obFormularioRC = new Formulario();
            $obFormularioRC->setForm(FALSE);
            $obFormularioRC->addComponente  ( $obLblReceitaCorrente );
            $obFormularioRC->addComponente  ( $obNuValorReceitaTributaria );
            $obFormularioRC->addComponente  ( $obNuValorIptu );
            $obFormularioRC->addComponente  ( $obNuValorIss );
            $obFormularioRC->addComponente  ( $obNuValorItbi );
            $obFormularioRC->addComponente  ( $obNuValorIrrf );
            $obFormularioRC->addComponente  ( $obNuValorOutrasReceitasTributarias );
            $obFormularioRC->addComponente  ( $obNuValorReceitaContribuicoes );
            $obFormularioRC->addComponente  ( $obNuValorReceitaPatrominial );
            $obFormularioRC->addComponente  ( $obNuValorReceitaAgropecuaria );
            $obFormularioRC->addComponente  ( $obNuValorReceitaIndustrial );
            $obFormularioRC->addComponente  ( $obNuValorReceitaServicos );
            $obFormularioRC->addComponente  ( $obNuValorTransferenciaCorrente );
            $obFormularioRC->addComponente  ( $obNuValorCotaParteFPM );
            $obFormularioRC->addComponente  ( $obNuValorCotaParteICMS );
            $obFormularioRC->addComponente  ( $obNuValorCotaParteIPVA );
            $obFormularioRC->addComponente  ( $obNuValorCotaParteITR );
            $obFormularioRC->addComponente  ( $obNuValorTransferenciaLC871996 );
            $obFormularioRC->addComponente  ( $obNuValorTransferenciaLC611989 );
            $obFormularioRC->addComponente  ( $obnuValorTransferenciasFundeb ); 
            $obFormularioRC->addComponente  ( $obnuValorOutrasTransferenciasCorrentes );
            $obFormularioRC->addComponente  ( $obnuValorOutrasReceitas );
            $obFormularioRC->addComponente  ( $obLblDeducoes );
            $obFormularioRC->addComponente  ( $obNuValorContribPlanoSSS );
            $obFormularioRC->addComponente  ( $obNuValorCompensacaoFinanceira );
            $obFormularioRC->addComponente  ( $obNuValorDeducaoFundeb );
            $obFormularioRC->addComponente  ( $obLblTotalRCL );
            $obFormularioRC->montaHTML();

            $obSpanReceitasCorrentes= new Span;
            $obSpanReceitasCorrentes->setId( 'spnReceitasCorrentes' );
            $obSpanReceitasCorrentes->setValue( $obFormularioRC->getHTML());
            $obSpanReceitasCorrentes->setStyle( "display: none; visibility: hidden;" );
            #Fim Receitas Correntes

            //Instancia um botao incluir para incluir os dados do formulario na lista
            $obBtnIncluir = new Button();
            $obBtnIncluir->setValue   ('Incluir');
            $obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirValorRCL','');");

            //Instancia um botao para limpar o formulario
            $obBtnLimpar = new Button();
            $obBtnLimpar->setValue   ('Limpar');
            $obBtnLimpar->setId      ('Limpar');
            $obBtnLimpar->obEvento->setOnClick ('limpaFormularioAux();');

            //Instancia um formulario
            $obFormulario = new Formulario();
            $obFormulario->addTitulo      ( $arParam['stTitle'] );
            $obFormulario->addComponente  ( $obITextBoxSelectEntidadeGeral );
            $obFormulario->addComponente  ( $obSlPeriodo );
            $obFormulario->addSpan        ($obSpanReceitasCorrentes);
            $obFormulario->addSpan        ($obSpanValor);
            $obFormulario->defineBarra    ( array($obBtnIncluir,$obBtnLimpar) );

            $obFormulario->montaInnerHTML();

            $stJs .= "jq('#spnFormAux').html('" . $obFormulario->getHTML() . "');";
        } else {
            $stJs .= "jq('#spnFormAux').html('');";
        }

        echo $stJs;
    }

    /**
     * Metodo montaFormulario, monta o formulario de vinculo de despesa pessoal
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array  $arParam Array de dados
     * @param string $stTitle String com o titulo para o formulario
     *
     * @return void
     */
    public function montaFormDespesaPessoal($arParam)
    {
        if ($arParam['stDataImplantacao'] != '') {
            //Inclui o componente ITextBoxSelectEntidadeGeral
            include CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';

            $arData = explode('/',$arParam['stDataImplantacao']);

            //Instancia o componente ITextBoxSelectEntidadeGeral
            $obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
            $obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("montaParametrosGET('buscaDadosPeriodo');");
            $obITextBoxSelectEntidadeGeral->inExercicio = $arData[2];
            $obITextBoxSelectEntidadeGeral->setObrigatorioBarra(true);

            //Preenche o array do periodo com os 12 meses anteriores a data de imp
            for ($i=($arData[1] - 1); $i > ($arData[1] - 14); $i--) {
                if ($i != '0') {
                    $inMes = $i;
                    $inAno = $arData[2];
                    if ($i < 0) {
                        $inMes = 13 + $i;
                        $inAno--;
                    }
                    if ($inAno != $arData[2])
                        $arPeriodo[(abs($inMes) . '/' . $inAno)] = ($this->arMes[abs($inMes)] . '/' . $inAno);
                }
            }

            //Instancia um select
            $obSlPeriodo = new Select();
            $obSlPeriodo->setName    ('stPeriodo');
            $obSlPeriodo->setId      ('stPeriodo');
            $obSlPeriodo->setRotulo  ('Período');
            $obSlPeriodo->setTitle   ('Informe o período.');
            $obSlPeriodo->addOption  ('', 'Selecione');
            foreach ($arPeriodo as $stKey => $stValue) {
                $obSlPeriodo->addOption($stKey, $stValue);
            }
            $obSlPeriodo->setObrigatorioBarra(true);
            $obSlPeriodo->obEvento->setOnChange("montaParametrosGET('habilitaDespesaPessoal');");

            $obLblTotalBrutaPessoal = new Label();
            $obLblTotalBrutaPessoal->setId('lblTotalBrutaPessoal');
            $obLblTotalBrutaPessoal->setRotulo('DESPESA BRUTA COM PESSOAL');

            //Valor Pessoal Ativo
            $obNuValorPessoalAtivo = new Numerico();
            $obNuValorPessoalAtivo->setName            ('nuValorPessoalAtivo');
            $obNuValorPessoalAtivo->setId              ('nuValorPessoalAtivo');
            $obNuValorPessoalAtivo->setRotulo          ('Pessoal Ativo');
            $obNuValorPessoalAtivo->setTitle           ('Despesa Pessoal Ativo');
            $obNuValorPessoalAtivo->setSize            (20);
            $obNuValorPessoalAtivo->setNegativo        (true);
            $obNuValorPessoalAtivo->setValue           ('0,00');
            $obNuValorPessoalAtivo->setObrigatorioBarra(true);
            $obNuValorPessoalAtivo->setMaxLength       (17);
            $obNuValorPessoalAtivo->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");
            
            //Valor Pessoal Inativo e Pensionista
            $obNuValorPessoalInativo = new Numerico();
            $obNuValorPessoalInativo->setName            ('nuValorPessoalInativo');
            $obNuValorPessoalInativo->setId              ('nuValorPessoalInativo');
            $obNuValorPessoalInativo->setRotulo          ('Pessoal Inativo e Pensionista');
            $obNuValorPessoalInativo->setTitle           ('Despesa Pessoal Inativo e Pensionista');
            $obNuValorPessoalInativo->setSize            (20);
            $obNuValorPessoalInativo->setNegativo        (true);
            $obNuValorPessoalInativo->setValue           ('0,00');
            $obNuValorPessoalInativo->setObrigatorioBarra(true);
            $obNuValorPessoalInativo->setMaxLength       (17);
            $obNuValorPessoalInativo->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");

            //Valor Outras Despesas Terceirização
            $obNuValorOutrasDespesas = new Numerico();
            $obNuValorOutrasDespesas->setName            ('nuValorOutrasDespesas');
            $obNuValorOutrasDespesas->setId              ('nuValorOutrasDespesas');
            $obNuValorOutrasDespesas->setRotulo          ('Outras Despesas Terceirização');
            $obNuValorOutrasDespesas->setTitle           ('Outras Despesas Terceirização');
            $obNuValorOutrasDespesas->setSize            (20);
            $obNuValorOutrasDespesas->setNegativo        (true);
            $obNuValorOutrasDespesas->setValue           ('0,00');
            $obNuValorOutrasDespesas->setObrigatorioBarra(true);
            $obNuValorOutrasDespesas->setMaxLength       (17);
            $obNuValorOutrasDespesas->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");
            
            $obLblTotalNaoComputadas = new Label();
            $obLblTotalNaoComputadas->setId('lblTotalNaoComputadas');
            $obLblTotalNaoComputadas->setRotulo('DESPESAS NÃO COMPUTADAS');

            //Valor Indenizações Demissão e IDV
            $obNuValorIndenizacoes = new Numerico();
            $obNuValorIndenizacoes->setName            ('nuValorIndenizacoes');
            $obNuValorIndenizacoes->setId              ('nuValorIndenizacoes');
            $obNuValorIndenizacoes->setRotulo          ('Indenizações Demissão e IDV');
            $obNuValorIndenizacoes->setTitle           ('Indenizações Demissão e IDV');
            $obNuValorIndenizacoes->setSize            (20);
            $obNuValorIndenizacoes->setNegativo        (true);
            $obNuValorIndenizacoes->setValue           ('0,00');
            $obNuValorIndenizacoes->setObrigatorioBarra(true);
            $obNuValorIndenizacoes->setMaxLength       (17);
            $obNuValorIndenizacoes->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");

            //Valor Decorrentes de Decisão Judicial
            $obNuValorDecisaoJudicial = new Numerico();
            $obNuValorDecisaoJudicial->setName            ('nuValorDecisaoJudicial');
            $obNuValorDecisaoJudicial->setId              ('nuValorDecisaoJudicial');
            $obNuValorDecisaoJudicial->setRotulo          ('Decorrentes de Decisão Judicial');
            $obNuValorDecisaoJudicial->setTitle           ('Decorrentes de Decisão Judicial');
            $obNuValorDecisaoJudicial->setSize            (20);
            $obNuValorDecisaoJudicial->setNegativo        (true);
            $obNuValorDecisaoJudicial->setValue           ('0,00');
            $obNuValorDecisaoJudicial->setObrigatorioBarra(true);
            $obNuValorDecisaoJudicial->setMaxLength       (17);
            $obNuValorDecisaoJudicial->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");

            //Valor Despesas Exercicios Anteriores
            $obNuValorExercicioAnterior = new Numerico();
            $obNuValorExercicioAnterior->setName            ('nuValorExercicioAnterior');
            $obNuValorExercicioAnterior->setId              ('nuValorExercicioAnterior');
            $obNuValorExercicioAnterior->setRotulo          ('Despesas Exercicios Anteriores');
            $obNuValorExercicioAnterior->setTitle           ('Despesas Exercicios Anteriores');
            $obNuValorExercicioAnterior->setSize            (20);
            $obNuValorExercicioAnterior->setNegativo        (true);
            $obNuValorExercicioAnterior->setValue           ('0,00');
            $obNuValorExercicioAnterior->setObrigatorioBarra(true);
            $obNuValorExercicioAnterior->setMaxLength       (17);
            $obNuValorExercicioAnterior->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");

            //Valor Inativos e Pensionistas com Recursos Vinculados
            $obNuValorInativosPensionista = new Numerico();
            $obNuValorInativosPensionista->setName            ('nuValorInativosPensionista');
            $obNuValorInativosPensionista->setId              ('nuValorInativosPensionista');
            $obNuValorInativosPensionista->setRotulo          ('Inativos e Pensionistas com Recursos Vinculados');
            $obNuValorInativosPensionista->setTitle           ('Inativos e Pensionistas com Recursos Vinculados');
            $obNuValorInativosPensionista->setSize            (20);
            $obNuValorInativosPensionista->setNegativo        (true);
            $obNuValorInativosPensionista->setValue           ('0,00');
            $obNuValorInativosPensionista->setObrigatorioBarra(true);
            $obNuValorInativosPensionista->setMaxLength       (17);
            $obNuValorInativosPensionista->obEvento->setOnChange(" montaParametrosGET('somaValores',''); ");

            $obLblTotalDP = new Label();
            $obLblTotalDP->setId('lblTotalDP');
            $obLblTotalDP->setRotulo('TOTAL DA DESPESA MENSAL ');

            #DESPESA BRUTA COM PESSOAL
            $obFormularioDB = new Formulario();
            $obFormularioDB->setForm(FALSE);
            $obFormularioDB->addComponente( $obLblTotalBrutaPessoal );
            $obFormularioDB->addComponente( $obNuValorPessoalAtivo );
            $obFormularioDB->addComponente( $obNuValorPessoalInativo );
            $obFormularioDB->addComponente( $obNuValorOutrasDespesas );
            $obFormularioDB->addComponente( $obLblTotalNaoComputadas );
            $obFormularioDB->addComponente( $obNuValorIndenizacoes );
            $obFormularioDB->addComponente( $obNuValorDecisaoJudicial );
            $obFormularioDB->addComponente( $obNuValorExercicioAnterior );
            $obFormularioDB->addComponente( $obNuValorInativosPensionista );
            $obFormularioDB->addComponente( $obLblTotalDP );
            $obFormularioDB->montaHTML();

            $obSpanDespesaBruta= new Span;
            $obSpanDespesaBruta->setId( 'spnDespesaBruta' );
            $obSpanDespesaBruta->setValue( $obFormularioDB->getHTML());
            $obSpanDespesaBruta->setStyle( "display: none; visibility: hidden;" );

            //Valor total de campos
            $obNumValorTotal = new Numerico();
            $obNumValorTotal->setName            ('flValor');
            $obNumValorTotal->setId              ('flValor');
            $obNumValorTotal->setRotulo          ('Valor');
            $obNumValorTotal->setTitle           ('Valor da Despesa Mensal');
            $obNumValorTotal->setSize            (20);
            $obNumValorTotal->setNegativo        (true);
            $obNumValorTotal->setValue           ('0,00');
            $obNumValorTotal->setObrigatorioBarra(true);
            $obNumValorTotal->setMaxLength       (17);
            
            $obFormularioValor = new Formulario();
            $obFormularioValor->setForm(FALSE);
            $obFormularioValor->addComponente  ( $obNumValorTotal );
            $obFormularioValor->montaHTML();

            $obSpanValor= new Span;
            $obSpanValor->setId( 'spnValorDP' );
            $obSpanValor->setValue( $obFormularioValor->getHTML());

            //Instancia um botao incluir para incluir os dados do formulario na lista
            $obBtnIncluir = new Button();
            $obBtnIncluir->setValue   ('Incluir');
            $obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirValorDespesaPessoal','');");

            //Instancia um botao para limpar o formulario
            $obBtnLimpar = new Button();
            $obBtnLimpar->setValue   ('Limpar');
            $obBtnLimpar->setId      ('Limpar');
            $obBtnLimpar->obEvento->setOnClick ('limpaFormularioAux();');

            //Instancia um formulario
            $obFormulario = new Formulario();
            $obFormulario->addTitulo    ( utf8_decode($arParam['stTitle']) );
            $obFormulario->addComponente( $obITextBoxSelectEntidadeGeral );
            $obFormulario->addComponente( $obSlPeriodo );
            $obFormulario->addSpan        ($obSpanDespesaBruta);
            $obFormulario->addSpan        ($obSpanValor);
            $obFormulario->defineBarra    (array($obBtnIncluir,$obBtnLimpar));

            $obFormulario->montaInnerHTML();

            $stJs .= "jq('#spnFormAux').html('" . $obFormulario->getHTML() . "');";
        } else {
            $stJs .= "jq('#spnFormAux').html('');";
        }

        echo $stJs;
    }

    public function somaValores()
    {
        $nuSomaTotal = 0.00;        
        $nuSomaTotal += $this->mascaraValor( $_REQUEST['nuValorPessoalAtivo'],false );
        $nuSomaTotal += $this->mascaraValor( $_REQUEST['nuValorPessoalInativo'],false );
        $nuSomaTotal += $this->mascaraValor( $_REQUEST['nuValorOutrasDespesas'],false );
        $nuSomaTotal -= $this->mascaraValor( $_REQUEST['nuValorIndenizacoes'],false );
        $nuSomaTotal -= $this->mascaraValor( $_REQUEST['nuValorDecisaoJudicial'],false );
        $nuSomaTotal -= $this->mascaraValor( $_REQUEST['nuValorExercicioAnterior'],false );
        $nuSomaTotal -= $this->mascaraValor( $_REQUEST['nuValorInativosPensionista'],false );
        
        $nuSomaTotal = $this->mascaraValor($nuSomaTotal,true);
         
        $stJs = " jq('#flValor').val('".$nuSomaTotal."'); ";

        echo $stJs;
    }

    public function somaValoresRCL()
    {   
        $nuTotal    = 0.00;
        $nuReceita  = 0.00;
        $nuDeducoes = 0.00;
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaTributaria']            ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorIptu']                         ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorIss']                          ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorItbi']                         ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorIrrf']                         ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorOutrasReceitasTributarias']    ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaContribuicoes']         ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaPatrominial']           ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaAgropecuaria']          ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaIndustrial']            ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorReceitaServicos']              ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorTransferenciaCorrente']        ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorCotaParteFPM']                 ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorCotaParteICMS']                ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorCotaParteIPVA']                ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorCotaParteITR']                 ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorTransferenciaLC871996']        ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorTransferenciaLC611989']        ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorTransferenciasFundeb']         ,false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorOutrasTransferenciasCorrentes'],false );
        $nuReceita += $this->mascaraValor( $_REQUEST['nuValorOutrasReceitas']               ,false );

        $nuDeducoes += $this->mascaraValor( $_REQUEST['nuValorContribPlanoSSS']             ,false );
        $nuDeducoes += $this->mascaraValor( $_REQUEST['nuValorCompensacaoFinanceira']       ,false );
        $nuDeducoes += $this->mascaraValor( $_REQUEST['nuValorDeducaoFundeb']               ,false );

        $nuTotal = ($nuReceita) - ($nuDeducoes);
        $nuTotal = $this->mascaraValor($nuTotal,true);
         
        $stJs = " jq('#flValor').val('".$nuTotal."'); ";

        echo $stJs;
    }

    public function mascaraValor($value, $boNumberBR = false)
    {    
        if($boNumberBR)
            $value = number_format($value,2,',','.');
        else
            $value = str_replace(',','.',str_replace('.','',$value));
        return $value;
    }

    /**
     * Metodo que monta a lista de valores para os periodos
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arPeriodo Array de cheques
     *
     * @return char
     */
    public function buildListaPeriodo($arPeriodo)
    {
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsPeriodo = new RecordSet();
        $rsPeriodo->preenche      ($arPeriodo);

        if ($rsPeriodo->getNumLinhas() > 0) {
            $rsPeriodo->addFormatacao('valor','NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsPeriodo);
            $table->setSummary  ('Lista de Valores');

            $table->Head->addCabecalho( 'Periodo',          70);
            $table->Head->addCabecalho( 'Valor',        20);

            $table->Body->addCampo('descricao', 'E');
            $table->Body->addCampo('valor'  , 'E');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCVincularReceitaCorrenteLiquida.php?Valor&id=%s','excluirValor')",
                                  array('id')
                                 );

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }

    /**
     * Metodo que monta a lista de providencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arProvidencia Array de providências
     *
     * @return char
     */
    public function buildListaProvidencia()
    {
        $arProvidencia = Sessao::read('arProvidencia');
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsProvidencia = new RecordSet();
        $rsProvidencia->preenche      ($arProvidencia);
        $rsProvidencia->addFormatacao('valor', 'NUMERIC_BR');

        if ($rsProvidencia->getNumLinhas() > 0) {
            $rsProvidencia->addFormatacao('valor','NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsProvidencia);
            $table->setSummary  ('Lista de providências');

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho('Providência', 85);
            $table->Head->addCabecalho('Valor', 15);

            $table->Body->addCampo('[descricao]', 'E');
            $table->Body->addCampo('[valor]', 'C');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCManterRiscosFiscais.php?Valor&cod_providencia=%s','excluirProvidenciaLista')",
                                  array('cod_providencia')
                                 );

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ," " ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }
    
    public function habilitaReceitaCorrente()
    {
        $obNumValor = new Numerico();
        $obNumValor->setName            ('flValor');
        $obNumValor->setId              ('flValor');
        $obNumValor->setObrigatorioBarra(true);
        $obNumValor->setNegativo        (true);

        $arPeriodo = explode('/', $_REQUEST['stDataImplantacao']);
        $inExercicio = $arPeriodo[2];

        if($inExercicio>=2016){
            $stJs  = " jq('#spnReceitasCorrentes').css('display', 'inline'); ";
            $stJs .= " jq('#spnReceitasCorrentes').css('visibility', 'visible'); ";

            $obNumValor->setRotulo          ('( Receitas Correntes - Deduções ) - Valor');
            $obNumValor->setTitle           ('Total da Receita Corrente Líquida ( Receitas Correntes - Deduções )');
            $obNumValor->setSize            (20);
            $obNumValor->setReadOnly        (true);
            $obNumValor->setDisabled        (true);
            $obNumValor->setMaxLength       (14);
            $obNumValor->setValue           ('0,00');
        }else{
            $stJs  = " jq('#spnReceitasCorrentes').css('display', 'none'); ";
            $stJs .= " jq('#spnReceitasCorrentes').css('visibility', 'hidden'); ";

            $obNumValor->setRotulo          ('Valor');
            $obNumValor->setTitle           ('Valor da Receita Corrente Líquida');
        }

        $obFormularioValor = new Formulario();
        $obFormularioValor->setForm(FALSE);
        $obFormularioValor->addComponente  ( $obNumValor );
        $obFormularioValor->montaInnerHTML();

        $stJs .= " jq('#spnValor').html('".$obFormularioValor->getHTML()."'); ";
        if($inExercicio>=2016)
            $stJs .= " montaParametrosGET('somaValoresRCL'); ";

        echo $stJs;
    }

    public function habilitaDespesaPessoal()
    {
        $obNumValor = new Numerico();
        $obNumValor->setName            ('flValor');
        $obNumValor->setId              ('flValor');
        $obNumValor->setObrigatorioBarra(true);
        $obNumValor->setNegativo        (true);

        $arPeriodo = explode('/', $_REQUEST['stDataImplantacao']);
        $inExercicio = $arPeriodo[2];

        if($inExercicio>=2016){
            $stJs  = " jq('#spnDespesaBruta').css('display', 'inline'); ";
            $stJs .= " jq('#spnDespesaBruta').css('visibility', 'visible'); ";

            $obNumValor->setRotulo          ('Total da Despesa Mensal');
            $obNumValor->setTitle           ('Total da Despesa Mensal');
            $obNumValor->setSize            (20);
            $obNumValor->setReadOnly        (true);
            $obNumValor->setDisabled        (true);
            $obNumValor->setValue           ('0,00');
        }else{
            $stJs  = " jq('#spnDespesaBruta').css('display', 'none'); ";
            $stJs .= " jq('#spnDespesaBruta').css('visibility', 'hidden'); ";

            $obNumValor->setRotulo          ('Valor');
            $obNumValor->setTitle           ('Valor da Receita Corrente Líquida');
        }

        $obFormularioValor = new Formulario();
        $obFormularioValor->setForm(FALSE);
        $obFormularioValor->addComponente  ( $obNumValor );
        $obFormularioValor->montaInnerHTML();

        $stJs .= " jq('#spnValorDP').html('".$obFormularioValor->getHTML()."'); ";
        if($inExercicio>=2016)
            $stJs .= " montaParametrosGET('somaValores'); ";

        echo $stJs;
    }

    /**
     * Metodo que monta a lista de valores para os periodos
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arPeriodo Array de cheques
     *
     * @return char
     */
    public function buildListaPeriodoAtuarial($arPeriodo)
    {
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        $rsPeriodo = new RecordSet();
        $rsPeriodo->preenche      ($arPeriodo);

        if ($rsPeriodo->getNumLinhas() > 0) {
            $rsPeriodo->addFormatacao('vl_receita_previdenciaria', 'NUMERIC_BR');
            $rsPeriodo->addFormatacao('vl_despesa_previdenciaria', 'NUMERIC_BR');
            $rsPeriodo->addFormatacao('vl_saldo_financeiro'      , 'NUMERIC_BR');

            $table = new Table ();
            $table->setRecordset($rsPeriodo);
            $table->setSummary  ('Lista de Valores');

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho( 'Exercício'                      , 10);
            $table->Head->addCabecalho( 'Valor da Receita Previdenciária', 20);
            $table->Head->addCabecalho( 'Valor da Despesa Previdenciária', 20);
            $table->Head->addCabecalho( 'Valor do Saldo Financeiro'      , 20);

            $table->Body->addCampo('exercicio'                , 'E');
            $table->Body->addCampo('vl_receita_previdenciaria', 'D');
            $table->Body->addCampo('vl_despesa_previdenciaria', 'D');
            $table->Body->addCampo('vl_saldo_financeiro'      , 'D');

            $table->Body->addAcao('excluir',
                                  "ajaxJavaScript('OCManterParametrosRREO13.php?id=%s','excluirValorAtuarial')",
                                  array('id')
                                 );

            $table->montaHTML();

            $stHTML = $table->getHtml();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
        }

        return $stHTML;
    }

    /**
     * Metodo incluirValor, cadastra na lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirValor($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stPeriodo'] == '') {
            $obErro->setDescricao('Selecione um período');
        } elseif ($arParam['flValor'] == '') {
            $obErro->setDescricao('Preencha o campo valor');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stPeriodo'] == ($arPeriodoAux['mes'] . '/' . $arPeriodoAux['ano']) ) {
                    $obErro->setDescricao('Periodo já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arData = explode('/',$arParam['stPeriodo']);
            $arPeriodo[$inCount]['id'          ] = $inCount;
            $arPeriodo[$inCount]['cod_entidade'] = $arParam['inCodEntidade'];
            $arPeriodo[$inCount]['mes'         ] = $arData[0];
            $arPeriodo[$inCount]['ano'         ] = $arData[1];
            $arPeriodo[$inCount]['valor'       ] = str_replace(',','.',str_replace('.','',$arParam['flValor'  ]));
            $arPeriodo[$inCount]['descricao'   ] = $this->arMes[$arData[0]] . '/' . $arData[1];

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    public function incluirValorRCL($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stPeriodo'] == '') {
            $obErro->setDescricao('Selecione um período');
        } elseif ($arParam['flValor'] == '') {
            $obErro->setDescricao('Preencha o campo valor');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stPeriodo'] == ($arPeriodoAux['mes'] . '/' . $arPeriodoAux['ano']) ) {
                    $obErro->setDescricao('Periodo já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arData = explode('/',$arParam['stPeriodo']);
            $arDataImplantacao = explode("/",$arParam["stDataImplantacao"]);

            $arPeriodo[$inCount]['id'          ] = $inCount;
            $arPeriodo[$inCount]['cod_entidade'] = $arParam['inCodEntidade'];
            $arPeriodo[$inCount]['mes'         ] = $arData[0];
            $arPeriodo[$inCount]['ano'         ] = $arData[1];

            if ( $arDataImplantacao[2] >= '2016' ) {
                $arPeriodo[$inCount]['valor_receita_tributaria']              = $this->mascaraValor($arParam['nuValorReceitaTributaria']             ,false);
                $arPeriodo[$inCount]['valor_iptu']                            = $this->mascaraValor($arParam['nuValorIptu']                          ,false);
                $arPeriodo[$inCount]['valor_iss']                             = $this->mascaraValor($arParam['nuValorIss']                           ,false);
                $arPeriodo[$inCount]['valor_itbi']                            = $this->mascaraValor($arParam['nuValorItbi']                          ,false);
                $arPeriodo[$inCount]['valor_irrf']                            = $this->mascaraValor($arParam['nuValorIrrf']                          ,false);
                $arPeriodo[$inCount]['valor_outras_receitas_tributarias']     = $this->mascaraValor($arParam['nuValorOutrasReceitasTributarias']     ,false);
                $arPeriodo[$inCount]['valor_receita_contribuicoes']           = $this->mascaraValor($arParam['nuValorReceitaContribuicoes']          ,false);
                $arPeriodo[$inCount]['valor_receita_patrimonial']             = $this->mascaraValor($arParam['nuValorReceitaPatrominial']            ,false);
                $arPeriodo[$inCount]['valor_receita_agropecuaria']            = $this->mascaraValor($arParam['nuValorReceitaAgropecuaria']           ,false);
                $arPeriodo[$inCount]['valor_receita_industrial']              = $this->mascaraValor($arParam['nuValorReceitaIndustrial']             ,false);
                $arPeriodo[$inCount]['valor_receita_servicos']                = $this->mascaraValor($arParam['nuValorReceitaServicos']               ,false);
                $arPeriodo[$inCount]['valor_transferencias_correntes']        = $this->mascaraValor($arParam['nuValorTransferenciaCorrente']         ,false);
                $arPeriodo[$inCount]['valor_cota_parte_fpm']                  = $this->mascaraValor($arParam['nuValorCotaParteFPM']                  ,false );
                $arPeriodo[$inCount]['valor_cota_parte_icms']                 = $this->mascaraValor($arParam['nuValorCotaParteICMS']                 ,false );
                $arPeriodo[$inCount]['valor_cota_parte_ipva']                 = $this->mascaraValor($arParam['nuValorCotaParteIPVA']                 ,false );
                $arPeriodo[$inCount]['valor_cota_parte_itr']                  = $this->mascaraValor($arParam['nuValorCotaParteITR']                  ,false );
                $arPeriodo[$inCount]['valor_transferencias_lc_871996']        = $this->mascaraValor($arParam['nuValorTransferenciaLC871996']         ,false );
                $arPeriodo[$inCount]['valor_transferencias_lc_611989']        = $this->mascaraValor($arParam['nuValorTransferenciaLC611989']         ,false );
                $arPeriodo[$inCount]['valor_transferencias_fundeb']           = $this->mascaraValor($arParam['nuValorTransferenciasFundeb']          ,false );
                $arPeriodo[$inCount]['valor_outras_transferencias_correntes'] = $this->mascaraValor($arParam['nuValorOutrasTransferenciasCorrentes'] ,false );
        
                $arPeriodo[$inCount]['valor_outras_receitas']                 = $this->mascaraValor($arParam['nuValorOutrasReceitas']                ,false);
                $arPeriodo[$inCount]['valor_contrib_plano_sss']               = $this->mascaraValor($arParam['nuValorContribPlanoSSS']               ,false);
                $arPeriodo[$inCount]['valor_compensacao_financeira']          = $this->mascaraValor($arParam['nuValorCompensacaoFinanceira']         ,false);
                $arPeriodo[$inCount]['valor_deducao_fundeb']                  = $this->mascaraValor($arParam['nuValorDeducaoFundeb']                 ,false);
                
                
                $nuSomaTotal = 0.00;
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaTributaria']            ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorIptu']                         ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorIss']                          ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorItbi']                         ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorIrrf']                         ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorOutrasReceitasTributarias']    ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaContribuicoes']         ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaPatrominial']           ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaAgropecuaria']          ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaIndustrial']            ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorReceitaServicos']              ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorTransferenciaCorrente']        ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorCotaParteFPM']                 ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorCotaParteICMS']                ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorCotaParteIPVA']                ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorCotaParteITR']                 ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorTransferenciaLC871996']        ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorTransferenciaLC611989']        ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorTransferenciasFundeb']         ,false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorOutrasTransferenciasCorrentes'],false );
                
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorOutrasReceitas']               ,false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorContribPlanoSSS']              ,false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorCompensacaoFinanceira']        ,false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorDeducaoFundeb']                ,false );

                $arParam['flValor'] = $this->mascaraValor($nuSomaTotal,true);
            }
            $arPeriodo[$inCount]['valor'       ] = $this->mascaraValor($arParam['flValor'],false);
            $arPeriodo[$inCount]['descricao'   ] = $this->arMes[abs($arData[0])] . '/' . $arData[1];

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
            $stJs .= "alertaAviso('Incluído Período ".$this->arMes[$arData[0]].'/'.$arData[1]."','form','unica','".Sessao::getId()."');";
            $stJs .= "jq('#stDataImplantacao').focus();";
            $stJs .= "jq('#stPeriodo').focus();";
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    public function incluirValorDespesaPessoal($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stPeriodo'] == '') {
            $obErro->setDescricao('Selecione um período');
        } elseif ($arParam['flValor'] == '') {
            $obErro->setDescricao('Preencha o campo valor');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stPeriodo'] == ($arPeriodoAux['mes'] . '/' . $arPeriodoAux['ano']) ) {
                    $obErro->setDescricao('Periodo já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arData = explode('/',$arParam['stPeriodo']);
            $arDataImplantacao = explode("/",$arParam['stDataImplantacao']);
            $arPeriodo[$inCount]['id']           = $inCount;
            $arPeriodo[$inCount]['cod_entidade'] = $arParam['inCodEntidade'];
            $arPeriodo[$inCount]['mes']          = $arData[0];
            $arPeriodo[$inCount]['ano']          = $arData[1];
            $arPeriodo[$inCount]['descricao']    = $this->arMes[$arData[0]] . '/' . $arData[1];
            if ( $arDataImplantacao[2] >= '2016' ) {
                $arPeriodo[$inCount]['valor_pessoal_ativo']         = $this->mascaraValor($arParam['nuValorPessoalAtivo'],false);
                $arPeriodo[$inCount]['valor_pessoal_inativo']       = $this->mascaraValor($arParam['nuValorPessoalInativo'],false);
                $arPeriodo[$inCount]['valor_terceirizacao']         = $this->mascaraValor($arParam['nuValorOutrasDespesas'],false);
                $arPeriodo[$inCount]['valor_indenizacoes']          = $this->mascaraValor($arParam['nuValorIndenizacoes'],false);
                $arPeriodo[$inCount]['valor_decisao_judicial']      = $this->mascaraValor($arParam['nuValorDecisaoJudicial'],false);
                $arPeriodo[$inCount]['valor_exercicios_anteriores'] = $this->mascaraValor($arParam['nuValorExercicioAnterior'],false);
                $arPeriodo[$inCount]['valor_inativos_pensionistas'] = $this->mascaraValor($arParam['nuValorInativosPensionista'],false);

                $nuSomaTotal = 0.00;
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorPessoalAtivo'],false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorPessoalInativo'],false );
                $nuSomaTotal += $this->mascaraValor( $arParam['nuValorOutrasDespesas'],false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorIndenizacoes'],false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorDecisaoJudicial'],false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorExercicioAnterior'],false );
                $nuSomaTotal -= $this->mascaraValor( $arParam['nuValorInativosPensionista'],false );

                $arParam['flValor'] = $this->mascaraValor($nuSomaTotal,true);
            }

            $arPeriodo[$inCount]['valor'] = $this->mascaraValor($arParam['flValor'],false);

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
            $stJs .= "alertaAviso('Incluído Período ".$this->arMes[$arData[0]].'/'.$arData[1]."','form','unica','".Sessao::getId()."');";
            $stJs .= "jq('#stDataImplantacao').focus();";
            $stJs .= "jq('#stPeriodo').focus();";
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

    /**
     * Metodo incluirProvidenciaLista, cadastra na lista uma providencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirProvidenciaLista($arParam)
    {
        $obErro = new Erro();
        if ($arParam['stProvidencia'] == '') {
            $obErro->setDescricao('Campo Providência não pode ser nulo.');
        }
        if ($arParam['flValorProvidencia'] == '' || $arParam['flValorProvidencia'] == '0,00') {
            $obErro->setDescricao('Campo Valor Providência não pode ser nulo ou igual a zero.');
            $stJs .= "jq('#flValorProvidencia').val('');";
            $stJs .= "jq('#flValorProvidencia').focus();";
        }
        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe primeiro o Valor do Risco.');
            $stJs .= "jq('#flValor').focus();";
        } elseif ($arParam['flValor'] == '0,00') {
            $obErro->setDescricao('Valor do Risco não pode ser zero.');
            $stJs .= "jq('#flValor').val('');";
            $stJs .= "jq('#flValor').focus();";
        }
        if (!$obErro->ocorreu()) {
            $arProvidencia = (array) Sessao::read('arProvidencia');
            $flValorProvidencia = str_replace('.', '', $arParam['flValorProvidencia']);
            $flValorProvidencia = str_replace(',', '.', $flValorProvidencia);
            $flValorTMP = $flValorProvidencia;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);

            if ($arProvidencia) {
                foreach ($arProvidencia AS $arTMP) {
                    $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
                }
            }

            if ($flValorTMP > $flValorRisco) {
                $obErro->setDescricao('A soma dos Valores das providências não pode ultrapassar o valor do risco.');
                $stJs .= "jq('#flValorProvidencia').val('');";
                $stJs .= "jq('#flValorProvidencia').focus();";
            }
        }

        $inCount = count($arProvidencia);
        if (!$obErro->ocorreu()) {
            $arProvidencia[$inCount]['id'                   ] = $inCount;
            $arProvidencia[$inCount]['cod_providencia'      ] = $inCount+1;
            $stProvidencia = str_replace('\\n', "\n", $arParam['stProvidencia']);
            $stProvidencia = stripslashes($stProvidencia);
            $arProvidencia[$inCount]['descricao'] = $stProvidencia;
            $arProvidencia[$inCount]['valor'] = $flValorProvidencia;

            Sessao::write('arProvidencia',$arProvidencia);

            $this->montaListaProvidencia();
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
            echo $stJs;
        }
    }

    /**
     * Metodo montaListaProvidencia, monta a lista das providências
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function montaListaProvidencia()
    {
        $stJs .= "jq('#spnLista').html('" . $this->buildListaProvidencia() . "');";
        $stJs .= 'limpaFormularioAux();';

        echo $stJs;
    }

    /**
     * Metodo incluirValorAtuarial, cadastra na lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirValorAtuarial($arParam)
    {
        $obErro = new Erro();
        if ($arParam['inCodEntidade'] == '') {

        } elseif ($arParam['stAno'] == '') {
            $obErro->setDescricao('Selecione um exercício');
        } elseif ($arParam['flDespesaPrevidenciaria'] == '') {
            $obErro->setDescricao('Preencha o campo despesa previdenciária');
        } elseif ($arParam['flReceitaPrevidenciaria'] == '') {
            $obErro->setDescricao('Preencha o campo receita previdenciária');
        } elseif ($arParam['flSaldoFinanceiro'] == '') {
            $obErro->setDescricao('Preencha o campo saldo financeiro');
        }
        if (!$obErro->ocorreu()) {
            $arPeriodo = (array) Sessao::read('arPeriodo');
            foreach ($arPeriodo as $arPeriodoAux) {
                if ($arParam['stAno'] == $arPeriodoAux['exercicio']) {
                    $obErro->setDescricao('Este exercício já está na lista');
                    break;
                }
            }
        }

        $inCount = count($arPeriodo);
        if (!$obErro->ocorreu()) {
            $arPeriodo[$inCount]['id'                       ] = $inCount;
            $arPeriodo[$inCount]['exercicio'                ] = $arParam['stAno'];
            $arPeriodo[$inCount]['vl_despesa_previdenciaria'] = str_replace(',','.',str_replace('.','',$arParam['flDespesaPrevidenciaria']));
            $arPeriodo[$inCount]['vl_receita_previdenciaria'] = str_replace(',','.',str_replace('.','',$arParam['flReceitaPrevidenciaria']));
            $arPeriodo[$inCount]['vl_saldo_financeiro'      ] = str_replace(',','.',str_replace('.','',$arParam['flSaldoFinanceiro']));

            Sessao::write('arPeriodo',$arPeriodo);

            $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial($arPeriodo) . "');";
            $stJs .= 'limpaFormularioAux();';
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    /**
     * Metodo excluirValor, remove um valor da lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirValor($arParam)
    {
        $arPeriodo = Sessao::read('arPeriodo');
        foreach ($arPeriodo as $arAux) {
            if ($arAux['id'] != $arParam['id']) {
                $arPeriodoNew[] = $arAux;
            }
        }

        Sessao::write('arPeriodo', (array) $arPeriodoNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo((array) $arPeriodoNew) . "');";
        echo $stJs;
    }

    /**
     * Metodo excluirProvidenciaLista, remove um valor da lista de providencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirProvidenciaLista($arParam)
    {
        $arProvidencia = Sessao::read('arProvidencia');
        $inCount = 0;
        foreach ($arProvidencia as $arAux) {
            if ($arAux['cod_providencia'] != $arParam['cod_providencia']) {
                $arProvidenciaNew[$inCount]['cod_providencia'] = $inCount + 1;
                $arProvidenciaNew[$inCount]['descricao'] = $arAux['descricao'];
                $arProvidenciaNew[$inCount]['valor'] = $arAux['valor'];
                $inCount++;
            }
        }

        Sessao::write('arProvidencia', (array) $arProvidenciaNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaProvidencia() . "');";
        echo $stJs;
    }

    /**
     * Metodo excluirValor, remove um valor da lista o valor para o periodo
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirValorAtuarial($arParam)
    {
        $arPeriodo = Sessao::read('arPeriodo');
        foreach ($arPeriodo as $arAux) {
            if ($arAux['id'] != $arParam['id']) {
                $arPeriodoNew[] = $arAux;
            }
        }

        Sessao::write('arPeriodo', (array) $arPeriodoNew);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial((array) $arPeriodoNew) . "');";
        echo $stJs;
    }

    /**
     * Metodo limpaSessao, remove os dados setados na sessao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function limpaSessao($arParam)
    {
        Sessao::remove('arPeriodo');
        $stJs .= "jq('#spnLista').html('" . array() . "');";
    }

    /**
     * Metodo limpaSessaoProvidencia, remove os dados setados na sessao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function limpaSessaoProvidencia($arParam)
    {
        Sessao::remove('arProvidencia');
        $stJs .= "jq('#spnLista').html('" . array() . "');";
    }

    /**
     * Metodo buscaDadosPeriodo, inclui na sessao e monta a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaDadosPeriodo($arParam)
    {
        $arPeriodo = array();
        if ($arParam['inCodEntidade'] != '') {
            //Pega a data de implantacao e separa os dados
            $arData = explode('/',$arParam['stDataImplantacao']);

            //Seta os dados para o filtro
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];

            switch ($arParam['stAcao']) {
            case 'incluirRCL':
                $this->obModel->listValorRCL($rsPeriodo);
                break;
            case 'incluirDP':
                $this->obModel->listValorDP($rsPeriodo);
                break;
            }
            
            if ($rsPeriodo->getNumLinhas() > 0) {
                while (!$rsPeriodo->eof()) {
                    $arPeriodo[] = array(  'id'           => count($arPeriodo)
                                          ,'cod_entidade' => $rsPeriodo->getCampo('cod_entidade')
                                          ,'mes'          => $rsPeriodo->getCampo('mes')
                                          ,'ano'          => $rsPeriodo->getCampo('ano')
                                          ,'valor'        => $rsPeriodo->getCampo('valor')
                                          ,'descricao'    => $this->arMes[$rsPeriodo->getCampo('mes')] . '/' . $rsPeriodo->getCampo('ano')
                                        );

                    $rsPeriodo->proximo();
                }
            }
        }
        Sessao::write('arPeriodo', $arPeriodo);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodo($arPeriodo) . "');";

        echo $stJs;

    }

    /**
     * Metodo buscaDadosPeriodo, inclui na sessao e monta a lista
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaValoresAtuariais($arParam)
    {
        $arPeriodo = array();
        if ($arParam['inCodEntidade'] != '') {
            //Seta os dados para o filtro
            $this->obModel->obROrcamentoEntidade->stExercicio      = Sessao::read('exercicio');
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];

            $this->obModel->listValorRREO13($rsPeriodo);

            if ($rsPeriodo->getNumLinhas() > 0) {
                while (!$rsPeriodo->eof()) {
                    $arPeriodo[] = array(  'id'                               => count($arPeriodo)
                                          ,'exercicio'                        => $rsPeriodo->getCampo('ano')
                                          ,'vl_despesa_previdenciaria'        => $rsPeriodo->getCampo('vl_despesa_previdenciaria')
                                          ,'vl_receita_previdenciaria'        => $rsPeriodo->getCampo('vl_receita_previdenciaria')
                                          ,'vl_saldo_financeiro'              => $rsPeriodo->getCampo('vl_saldo_financeiro')
                                        );

                    $rsPeriodo->proximo();
                }
            }
        }
        Sessao::write('arPeriodo', $arPeriodo);

        $stJs .= "jq('#spnLista').html('" . $this->buildListaPeriodoAtuarial($arPeriodo) . "');";

        echo $stJs;
    }

    /**
     * Metodo buscaEntidades
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaEntidades($arParam)
    {
        $this->obModel->obROrcamentoEntidade->stExercicio = $arParam['stExercicio'];
        $stOrder = " cod_entidade ";
        $this->obModel->obROrcamentoEntidade->listar($rsEntidades, $stOrder);
        if ($arParam['stExercicio'] != '') {
            $stJs = "limpaSelect(f.inCodEntidadeDisponivel,0);";
            $stJs = "limpaSelect(f.inCodEntidade,0);";
            if ($rsEntidades->getNumLinhas() > -1) {
                $inContador = 0;
                while (!$rsEntidades->EOF()) {
                    $inCodEntidade = $rsEntidades->getCampo('cod_entidade');
                    $stNomEntidade = $rsEntidades->getCampo('nom_cgm');

                    $stJs .= "f.inCodEntidadeDisponivel.options[$inContador] = new Option('".$stNomEntidade."','".$inCodEntidade."'); \n";
                    $inContador++;
                    $rsEntidades->proximo();
                    $stJs .= "jq('#inCodEntidade').focus();";
                }

            } else {
                $stJs .= "jq('#stExercicio').val('');";
                $stJs .= "alertaAviso('Não existe entidade cadastrada para este exercício.','form','erro','".Sessao::getId()."');";
                $stJs .= "jq('#stExercicio').focus();";
            }
        }

        if ($stJs) {
            echo $stJs;
        }
    }

    /**
     * Metodo verificaDataImplantacao, verifica se a rotina ja foi usada
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function verificaDataImplantacao($arParam)
    {

        $this->obModel->recuperaDataImplantacao($stDataImplantacao);

        if ($stDataImplantacao != '') {
            switch ($arParam['stAcao']) {
            case 'incluirRCL':
                $pgOcul = 'OCVincularReceitaCorrenteLiquida.php';
                $stTitle = 'Dados da Receita Corrente Líquida';
                $stCtrl = 'montaFormRCL';
                break;
            case 'incluirDP':
                $pgOcul = 'OCVincularDespesaPessoal.php';
                $stTitle = 'Dados da Despesa Pessoal';
                $stCtrl = 'montaFormDespesaPessoal';
                break;
            }

            //Se existir, seta a data de implantacao e deixa ela como readonly
            $stJs .= "jq('#stDataImplantacao').val('" . $stDataImplantacao . "');";
            $stJs .= "jq('#stDataImplantacao').attr('readonly', 'readonly');";

            $stJs .= "ajaxJavaScript('" . $pgOcul . "?stDataImplantacao='+this.value+'&stTitle=" . utf8_encode($stTitle) . "',".$stCtrl.");";
        }

        echo $stJs;
    }

    /**
     * Metodo incluirRCL, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirRCL($arParam)
    {
        $obErro = new Erro();
        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            $this->obModel->stDataImplantacao = $arParam['stDataImplantacao'];
            $obErro = $this->obModel->incluirDataImplantacao(false, $boTransacao);

            $arData = explode('/', $arParam['stDataImplantacao']);

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->listValorRCL($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $arPeriodoDB[$rsPeriodo->getCampo('mes') . '-' . $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('valor')] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                if (!isset($arPeriodoDB[$arAux['mes'] . '-' . $arAux['ano'] . '-' . $arAux['valor']])) {

                    $this->obModel->inMes                                  = $arAux['mes'];
                    $this->obModel->inAno                                  = $arAux['ano'];
                    $this->obModel->nuValorReceitaTributaria             = ($arData[2] >= '2016') ? $arAux['valor_receita_tributaria']               : 0.00;
                    $this->obModel->nuValorIptu                          = ($arData[2] >= '2016') ? $arAux['valor_iptu']                             : 0.00;
                    $this->obModel->nuValorIss                           = ($arData[2] >= '2016') ? $arAux['valor_iss']                              : 0.00;
                    $this->obModel->nuValorItbi                          = ($arData[2] >= '2016') ? $arAux['valor_itbi']                             : 0.00;
                    $this->obModel->nuValorIrrf                          = ($arData[2] >= '2016') ? $arAux['valor_irrf']                             : 0.00;
                    $this->obModel->nuValorOutrasReceitasTributarias     = ($arData[2] >= '2016') ? $arAux['valor_outras_receitas_tributarias']      : 0.00;
                    $this->obModel->nuValorReceitaContribuicoes          = ($arData[2] >= '2016') ? $arAux['valor_receita_contribuicoes']            : 0.00;
                    $this->obModel->nuValorReceitaPatrimonial            = ($arData[2] >= '2016') ? $arAux['valor_receita_patrimonial']              : 0.00;
                    $this->obModel->nuValorReceitaAgropecuaria           = ($arData[2] >= '2016') ? $arAux['valor_receita_agropecuaria']             : 0.00;
                    $this->obModel->nuValorReceitaIndustrial             = ($arData[2] >= '2016') ? $arAux['valor_receita_industrial']               : 0.00;
                    $this->obModel->nuValorReceitaServicos               = ($arData[2] >= '2016') ? $arAux['valor_receita_servicos']                 : 0.00;
                    $this->obModel->nuValorTransferenciaCorrente         = ($arData[2] >= '2016') ? $arAux['valor_transferencias_correntes']         : 0.00;
                    $this->obModel->nuValorCotaParteFPM                  = ($arData[2] >= '2016') ? $arAux['valor_cota_parte_fpm']                   : 0.00;
                    $this->obModel->nuValorCotaParteICMS                 = ($arData[2] >= '2016') ? $arAux['valor_cota_parte_icms']                  : 0.00;
                    $this->obModel->nuValorCotaParteIPVA                 = ($arData[2] >= '2016') ? $arAux['valor_cota_parte_ipva']                  : 0.00;
                    $this->obModel->nuValorCotaParteITR                  = ($arData[2] >= '2016') ? $arAux['valor_cota_parte_itr']                   : 0.00;
                    $this->obModel->nuValorTransferenciaLC871996         = ($arData[2] >= '2016') ? $arAux['valor_transferencias_lc_871996']         : 0.00;
                    $this->obModel->nuValorTransferenciaLC611989         = ($arData[2] >= '2016') ? $arAux['valor_transferencias_lc_611989']         : 0.00;
                    $this->obModel->nuValorTransferenciasFundeb          = ($arData[2] >= '2016') ? $arAux['valor_transferencias_fundeb']            : 0.00;
                    $this->obModel->nuValorOutrasTransferenciasCorrentes = ($arData[2] >= '2016') ? $arAux['valor_outras_transferencias_correntes']  : 0.00;
                    $this->obModel->nuValorOutrasReceitas                = ($arData[2] >= '2016') ? $arAux['valor_outras_receitas']                  : 0.00;
                    $this->obModel->nuValorContribPlanoSSS               = ($arData[2] >= '2016') ? $arAux['valor_contrib_plano_sss']                : 0.00;
                    $this->obModel->nuValorCompensacaoFinanceira         = ($arData[2] >= '2016') ? $arAux['valor_compensacao_financeira']           : 0.00;
                    $this->obModel->nuValorDeducaoFundeb                 = ($arData[2] >= '2016') ? $arAux['valor_deducao_fundeb']                   : 0.00;
                    $this->obModel->flValor = $arAux['valor'];

                    $obErro = $this->obModel->vincularReceitaCorrenteLiquida(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inMes, $inAno, $flValor) = explode('-', $arDelete);
                    if ($inMes . '-' . $inAno == $arAux['mes'] . '-' . $arAux['ano']) {
                        unset($arPeriodoDB[$inMes . '-' . $inAno . '-' . $flValor]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inMes, $inAno) = explode('-', $arAux);
                $this->obModel->inMes                                  = $inMes;
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirReceitaCorrenteLiquida(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMVincularReceitaCorrenteLiquida.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Receita Corrente Líquida vinculada com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }
        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirDP, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirDP($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            $this->obModel->stDataImplantacao = $arParam['stDataImplantacao'];
            $obErro = $this->obModel->incluirDataImplantacao(false, $boTransacao);

            $arData = explode('/', $arParam['stDataImplantacao']);

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = $arData[2];
            $this->obModel->listValorDP($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $arPeriodoDB[$rsPeriodo->getCampo('mes') . '-' . $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('valor')] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                if (!isset($arPeriodoDB[$arAux['mes'] . '-' . $arAux['ano'] . '-' . $arAux['valor']])) {
                    $this->obModel->inMes                      = $arAux['mes'];
                    $this->obModel->inAno                      = $arAux['ano'];
                    $this->obModel->nuValorPessoalAtivo        = ($arData[2] >= '2016') ? $arAux['valor_pessoal_ativo']          : 0.00;
                    $this->obModel->nuValorPessoalInativo      = ($arData[2] >= '2016') ? $arAux['valor_pessoal_inativo']        : 0.00;
                    $this->obModel->nuValorOutrasDespesas      = ($arData[2] >= '2016') ? $arAux['valor_terceirizacao']          : 0.00;
                    $this->obModel->nuValorIndenizacoes        = ($arData[2] >= '2016') ? $arAux['valor_indenizacoes']           : 0.00;
                    $this->obModel->nuValorDecisaoJudicial     = ($arData[2] >= '2016') ? $arAux['valor_decisao_judicial']       : 0.00;
                    $this->obModel->nuValorExercicioAnterior   = ($arData[2] >= '2016') ? $arAux['valor_exercicios_anteriores']  : 0.00;
                    $this->obModel->nuValorInativosPensionista = ($arData[2] >= '2016') ? $arAux['valor_inativos_pensionistas']  : 0.00;
                    $this->obModel->flValor                    = $arAux['valor'];

                    $obErro = $this->obModel->vincularDespesaPessoal(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inMes, $inAno, $flValor) = explode('-', $arDelete);
                    if ($inMes . '-' . $inAno == $arAux['mes'] . '-' . $arAux['ano']) {
                        unset($arPeriodoDB[$inMes . '-' . $inAno . '-' . $flValor]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inMes, $inAno, $flValor) = explode('-', $arAux);
                $this->obModel->inMes                                  = $inMes;
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirDespesaPessoal(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMVincularDespesaPessoal.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Despesa Pessoal vinculada com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirRREO13, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirRREO13($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a entidade');
        }

        if (!$obErro->ocorreu()) {
            $arPeriodo = Sessao::read('arPeriodo');

            //Lista os periodos cadastrados no banco
            $this->obModel->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obROrcamentoEntidade->stExercicio      = Sessao::read('exercicio');
            $this->obModel->listValorRREO13($rsPeriodo);

            while (!$rsPeriodo->eof()) {
                $stChave = $rsPeriodo->getCampo('ano') . '-' . $rsPeriodo->getCampo('vl_receita_previdenciaria') . '-';
                $stChave.= $rsPeriodo->getCampo('vl_despesa_previdenciaria') . '-' . $rsPeriodo->getCampo('vl_saldo_financeiro');
                $arPeriodoDB[$stChave] = true;
                $rsPeriodo->proximo();
            }

            //Inclui os periodos que nao existem na basa
            foreach ((array) $arPeriodo as $arAux) {
                $stChave = $arAux['exercicio'] . '-' . $arAux['vl_receita_previdenciaria'] . '-';
                $stChave.= $arAux['vl_despesa_previdenciaria'] . '-' . $arAux['vl_saldo_financeiro'];
                if (!isset($arPeriodoDB[$stChave])) {
                    $this->obModel->inAno                                  = $arAux['exercicio'];
                    $this->obModel->flReceitaPrevidenciaria                = $arAux['vl_receita_previdenciaria'];
                    $this->obModel->flDespesaPrevidenciaria                = $arAux['vl_despesa_previdenciaria'];
                    $this->obModel->flSaldoFinanceiro                      = $arAux['vl_saldo_financeiro'];

                    $obErro = $this->obModel->vincularParametrosRREO13(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }

                foreach ((array) $arPeriodoDB as $arDelete => $boValor) {
                    list($inAno, $flVlReceita, $flVlDespesa, $flVlSaldo) = explode('-', $arDelete);
                    if ($inAno == $arAux['exercicio']) {
                        unset($arPeriodoDB[$inAno . '-' . $flVlReceita . '-' . $flVlDespesa . '-' . $flVlSaldo]);
                    }
                }
            }

            //Deleta os que foram removidos
            foreach ((array) $arPeriodoDB as $arAux => $boValor) {
                list($inAno, $flVlReceita, $flVlDespesa, $flVlSaldo) = explode('-', $arAux);
                $this->obModel->inAno                                  = $inAno;

                $obErro = $this->obModel->excluirParametrosRREO13(false, $boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso('FMManterParametrosRREO13.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Parametros vinculados com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluirDemonstrativo, adiciona os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a Entidade');
        }

        if ($arParam['stDescricaoRisco'] == '') {
            $obErro->setDescricao('Informe a Descrição');
        }

        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe o Valor');
        }

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercício');
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->recuperaPPAExercicio($rsPPA);
            if ($rsPPA->getNumLinhas() < 1) {
                $obErro->setDescricao('Não existe um PPA para o exercício informado.');
            }
        }

        if (!$obErro->ocorreu()) {
            $arProvidencia = Sessao::read('arProvidencia');
            $flValorTMP = 0;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);
            if (!empty($arProvidencia)) {
                foreach ($arProvidencia as $arTMP) {
                    $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
                }
                if ($flValorTMP != $flValorRisco) {
                    $obErro->setDescricao('A soma do valor das providÊncias deve ser igual ao valor do risco fiscal.');
                }
            } else {
                $obErro->setDescricao('Deve haver pelo menos uma providência relacionada ao risco fiscal');
            }
        }

        if (!is_array($arParam['inCodEntidade'])) {
            $arParam['inCodEntidade'] = (array) $arParam['inCodEntidade'];
        }

        if (!$obErro->ocorreu()) {
            foreach ($arParam['inCodEntidade'] as $inCodEntidade) {
                $this->obModel->stDescricao = $arParam['stDescricaoRisco'];
                $this->obModel->flValor = $arParam['flValor'];
                $this->obModel->stExercicio = $arParam['stExercicio'];
                $this->obModel->inCodEntidade = $inCodEntidade;
                $this->obModel->inCodIdentificador = $arParam['inCodIdentificador'];
                $obErro = $this->obModel->incluirRiscosFiscais(false, $boTransacao);

                if (!$obErro->ocorreu()) {
                    //Inclui as Providencias que nao existem na base
                    foreach ($arProvidencia as $arAux) {
                        $this->obModel->inCodProvidencia   = $arAux['cod_providencia'];
                        $this->obModel->stDescricao        = $arAux['descricao'];
                        $this->obModel->flValorProvidencia = $arAux['valor'];

                        $obErro = $this->obModel->incluirProvidencias(false, $boTransacao);
                        if ($obErro->ocorreu()) {
                            break;
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                $arParam['stAcao'] = 'incluir';
                SistemaLegado::alertaAviso('FMManterRiscosFiscais.php?stAcao='.$arParam['stAcao'], $this->obModel->inCodRisco, $arParam['stAcao'], 'aviso', Sessao::getId(), '../');
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo alterarDemonstrativo, altera os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function alterarDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if ($arParam['inCodEntidade'] == '') {
            $obErro->setDescricao('Informe a Entidade');
        }

        if ($arParam['stDescricaoRisco'] == '') {
            $obErro->setDescricao('Informe a Descrição');
        }

        if ($arParam['flValor'] == '') {
            $obErro->setDescricao('Informe o Valor');
        }

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercicio');
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->recuperaPPAExercicio($rsPPA);
            if ($rsPPA->getNumLinhas() < 1) {
                $obErro->setDescricao('Não existe um PPA para o exercício informado.');
            }
        }

        if (!$obErro->ocorreu()) {
            $arProvidencia = Sessao::read('arProvidencia');
            $flValorTMP = 0;
            $flValorRisco = str_replace('.', '', $arParam['flValor']);
            $flValorRisco = str_replace(',', '.', $flValorRisco);
            foreach ($arProvidencia AS $arTMP) {
                $flValorTMP = bcadd($flValorTMP, $arTMP['valor'], 2);
            }
            if ($flValorTMP != $flValorRisco) {
                $obErro->setDescricao('A soma do valor das providências deve ser igual ao valor do risco fiscal.');
            }
        }

        if (!$obErro->ocorreu()) {
            $this->obModel->stDescricao = $arParam['stDescricaoRisco'];
            $this->obModel->flValor = $arParam['flValor'];
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->inCodEntidade = $arParam['inCodEntidade'];
            $this->obModel->inCodRisco = $arParam['inCodRisco'];
            $this->obModel->inCodIdentificador = $arParam['inCodIdentificador'];

            if (!$obErro->ocorreu()) {
                $this->obModel->listProvidencias($rsProvidencias);
                while (!$rsProvidencias->EOF()) {
                    $this->obModel->inCodProvidencia = $rsProvidencias->getCampo('cod_providencia');
                    $obErro = $this->obModel->excluirProvidencias(false, $boTransacao);
                    $rsProvidencias->proximo();
                }
            }

            if (!$obErro->ocorreu()) {
                $obErro = $this->obModel->incluirRiscosFiscais(false, $boTransacao);

                //Inclui as Providencias que nao existem na base
                $arProvidencia = Sessao::read('arProvidencia');
                foreach ($arProvidencia as $arAux) {
                    $this->obModel->inCodProvidencia   = $arAux['cod_providencia'];
                    $this->obModel->stDescricao        = $arAux['descricao'];
                    $this->obModel->flValorProvidencia = $arAux['valor'];

                    $obErro = $this->obModel->incluirProvidencias(false, $boTransacao);
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                $stFiltro .= "&stExercicio=".$arParam['stExercicio'];
                $stFiltro .= "&inCodEntidade=".$arParam['inCodEntidade'];
                $stFiltro .= "&stAcao=alterar";
                $arParam['stAcao'] = 'alterar';
                SistemaLegado::alertaAviso('LSManterRiscosFiscais.php'.'?'.Sessao::getId().$stFiltro, $arParam['inCodRisco'],$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_alterar', 'erro');
        }
    }

    /**
     * Metodo excluirDemonstrativo, altera os dados
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirDemonstrativo($arParam)
    {
        $obErro = new Erro();

        if (!$obErro->ocorreu()) {
            $this->obModel->stExercicio = $arParam['stExercicio'];
            $this->obModel->inCodEntidade = $arParam['inCodEntidade'];
            $this->obModel->inCodRisco = $arParam['inCodRisco'];

            if (!$obErro->ocorreu()) {
                $this->obModel->listProvidencias($rsProvidencias);
                while (!$rsProvidencias->EOF()) {
                    $this->obModel->inCodProvidencia = $rsProvidencias->getCampo('cod_providencia');
                    $obErro = $this->obModel->excluirProvidencias(false, $boTransacao);
                    $rsProvidencias->proximo();
                }
            }

            if (!$obErro->ocorreu()) {
                $obErro = $this->obModel->excluirRiscosFiscais(false, $boTransacao);
            }

            if (!$obErro->ocorreu()) {
                $stFiltro .= "&stExercicio=".$arParam['stExercicio'];
                $stFiltro .= "&inCodEntidade=".$arParam['inCodEntidade'];
                $stFiltro .= "&stAcao=excluir";
                $arParam['stAcao'] = 'excluir';
                SistemaLegado::alertaAviso('LSManterRiscosFiscais.php'.'?'.Sessao::getId().$stFiltro, $arParam['inCodRisco'],$arParam['stAcao'],'aviso', Sessao::getId(), "../");
            }

        }

        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_excluir', 'erro');
        }
    }

    /**
     * Metodo buldListaReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return string
     */
    public function buildListaReceitaAnexo3(array $arDados)
    {
        $rsReceita = new RecordSet;
        $rsReceita->preenche($arDados);

        $table = new Table;
        $table->setRecordset  ($rsReceita);
        $table->setSummary    ('Receitas Vinculadas');
        //$table->setConditional(true, '#efefef');

        $table->Head->addCabecalho('Tipo',20);
        $table->Head->addCabecalho('Exercicio', 10);
        $table->Head->addCabecalho('Receita'  , 70);

        $table->Body->addCampo('nom_tipo'                   ,'E');
        $table->Body->addCampo('exercicio'                  ,'C');
        $table->Body->addCampo('[cod_receita] - [descricao]','E');

        $table->Body->addAcao('excluir', "ajaxJavaScript('OCManterAnexo3RCL.php?&cod_receita=%s&cod_tipo=%s','excluirReceitaAnexo3');", array('cod_receita','cod_tipo'));

        $table->montaHTML(true);

        $stJs.= "\n jq('#spnLista').html('".$table->getHtml()."');";

        return $stJs;
    }

    /**
     * Metodo incluirReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return string
     */
    public function incluirReceitaAnexo3(array $arParam)
    {
        $obErro = new Erro();
        $arReceitas = (array) Sessao::read('receitas');

        if ($arParam['stExercicio'] == '') {
            $obErro->setDescricao('Informe o Exercício');
        } elseif ($arParam['inCodReceita'] == '') {
            $obErro->setDescricao('Informe a Receita');
        } elseif ($arParam['inCodTipo'] == '') {
            $obErro->setDescricao('Informe a Tipo da Receita');
        }

        if (!$obErro->ocorreu()) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $arParam['inCodReceita']) AND ($arReceita['cod_tipo'] == $arParam['inCodTipo'])) {
                    $obErro->setDescricao('A Receita já está na lista');
                }
            }
        }

        if (!$obErro->ocorreu()) {
            include CAM_GF_ORC_NEGOCIO . 'ROrcamentoReceita.class.php';
            $obROrcamentoReceita = new ROrcamentoReceita();
            $obROrcamentoReceita->setExercicio($arParam['stExercicio']);
            $obROrcamentoReceita->setCodReceita($arParam['inCodReceita']);
            $obROrcamentoReceita->listar($rsReceita);

            $this->obModel->inCodTipoReceita = $arParam['inCodTipo'];
            $this->obModel->listTipoReceitasAnexo3($rsTipo);

            $arReceitas[] = array(
                'exercicio'   => $arParam['stExercicio'],
                'cod_receita' => $arParam['inCodReceita'],
                'cod_tipo'    => $rsTipo->getCampo('cod_tipo'),
                'nom_tipo'    => $rsTipo->getCampo('descricao'),
                'descricao'   => $rsReceita->getCampo('descricao'),
                'new'         => true
            );
            $arReceitas = Sessao::write('receitas',$arReceitas);

            $stJs.= "jq('input#inCodReceita').val('');";
            $stJs.= "jq('#stNomReceita').html('&nbsp;');";
            $stJs.= "jq('#inCodTipo').selectOptions('',true);";
            $stJs.= $this->buildListaReceitaAnexo3($arReceitas);
        } else {
            $stJs.= "alertaAviso('" . $obErro->getDescricao() . ".','form','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

    /**
     * Metodo excluirReceitaAnexo3
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function excluirReceitaAnexo3(array $arParam)
    {
        $arReceitas    = (array) Sessao::read('receitas');
        $arReceitasDel = (array) Sessao::read('receitas_del');
        $arReceitasNew = array();
        if ($arParam['cod_receita']) {
            foreach ($arReceitas as $arReceita) {
                if (($arReceita['cod_receita'] == $arParam['cod_receita']) AND ($arReceita['cod_tipo'] == $arParam['cod_tipo'])) {
                    $arReceitasDel[] = $arReceita;
                } else {
                    $arReceitasNew[] = $arReceita;
                }
            }
        } else {
            $arReceitasNew = array();
        }
        Sessao::write('receitas'    ,$arReceitasNew);
        Sessao::write('receitas_del',$arReceitasDel);

        $stJs.= $this->buildListaReceitaAnexo3($arReceitasNew);

        echo $stJs;
    }

    /**
     * Metodo listReceitaAnexo3, retorna as receitas vinculadas
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function carregaReceitasAnexo3(array $arParam)
    {
        //recupera as receitas
        $obErro = $this->obModel->listReceitasAnexo3($rsReceitas);
        $arReceitas = array();
        while (!$rsReceitas->eof()) {
            $arReceitas[] = array (
                'exercicio'   => $rsReceitas->getCampo('exercicio'),
                'cod_receita' => $rsReceitas->getCampo('cod_receita'),
                'cod_tipo'    => $rsReceitas->getCampo('cod_tipo'),
                'nom_tipo'    => $rsReceitas->getCampo('nom_tipo'),
                'descricao'   => $rsReceitas->getCampo('descricao'),
                'new'         => false,
            );

            $rsReceitas->proximo();
        }

        Sessao::write('receitas',$arReceitas);

        //recupera a config do irrf
        $this->obModel->obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio());
        $this->obModel->obTAdministracaoConfiguracao->setDado('cod_modulo', 36);
        $this->obModel->obTAdministracaoConfiguracao->setDado('parametro', 'deduzir_irrf_anexo_3');
        $this->obModel->obTAdministracaoConfiguracao->recuperaPorChave($rsConfig);

        if ($rsConfig->getCampo('valor') == 'true') {
            $stJs .= "jq('select#boIRRF').selectOptions('1');";
        } elseif ($rsConfig->getCampo('valor') == 'false') {
            $stJs .= "jq('select#boIRRF').selectOptions('0');";
        }

        $stJs .= $this->buildListaReceitaAnexo3($arReceitas);

        echo $stJs;
    }

    /**
     * Metodo incluirAnexo3, faz a inclusao dos dados na base
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirAnexo3(array $arParam)
    {

        $obErro = new Erro();
        foreach ((array) Sessao::read('receitas_del') as $receita) {
            $this->obModel->inCodReceita      = $receita['cod_receita'];
            $this->obModel->stExercicio       = $receita['exercicio'];
            $this->obModel->inCodTipoReceita  = $receita['cod_tipo'];
            $obErro = $this->obModel->excluirReceitaAnexo3($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            foreach ((array) Sessao::read('receitas') as $receita) {
                if ($receita['new']) {
                    $this->obModel->inCodReceita = $receita['cod_receita'];
                    $this->obModel->stExercicio  = $receita['exercicio'];
                    $this->obModel->inCodTipoReceita  = $receita['cod_tipo'];
                    $obErro = $this->obModel->incluirReceitaAnexo3($boTransacao);
                }
            }
        }
        if (!$obErro->ocorreu()) {
            $this->obModel->boIRRF = $arParam['boIRRF'];
            $obErro = $this->obModel->alterarConfiguracaoReceitaAnexo3($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterAnexo3RCL.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Configuração concluída com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        }
    }

}
